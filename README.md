# stage-docker<!-- omit in toc -->

## Sommaire<!-- omit in toc -->

- [1. Introduction](#1-introduction)
- [2. Configuration](#2-configuration)
  - [2.1. Liste des paramètres](#21-liste-des-paramètres)
    - [2.1.1. Paramètres des conteneurs](#211-paramètres-des-conteneurs)
    - [2.1.2. Variables d'environnement](#212-variables-denvironnement)
  - [2.2. Configuration du webservice](#22-configuration-du-webservice)
  - [2.3. Configuration de l'explorateur PHP](#23-configuration-de-lexplorateur-php)
    - [2.3.1. Configuration du conteneur](#231-configuration-du-conteneur)
    - [2.3.2. Configuration de l'application](#232-configuration-de-lapplication)
      - [2.3.2.1. Fichiers de configuration](#2321-fichiers-de-configuration)
      - [2.3.2.2. Variables d'environnement](#2322-variables-denvironnement)
- [3. Commandes disponnibles](#3-commandes-disponnibles)
- [4. Première utilisation](#4-première-utilisation)
<br/>


## 1. Introduction

Ce dépôt contient les fichiers permettant de créer deux conteneurs :
* Un pour un webservice basé sur [Tomcat 9.0.48](https://tomcat.apache.org/tomcat-9.0-doc/introduction.html)
* Un pour un explorateur de fichiers PHP basé sur [EncodeExplorer](https://github.com/guillaume-elambert/BTS-stage_2020_encode-explorer)
<br/>


## 2. Configuration

Le fichier de configuration se trouve dans le fichier [`config`](./config).
Cette configuration permet de définir comment seronts construits et exécutés les conteneurs.
<br/><br/>

### 2.1. Liste des paramètres

Dans cette section se trouve la liste de l'ensemble des paramètres utilisés lors de la création et de l'exécution des conteneurs.
<br/><br/>

#### 2.1.1. Paramètres des conteneurs

Voici la liste des paramètres des conteneurs.

|     Paramètre      | Description                                                                                  |                                                          Format                                                           |
| :----------------: | :------------------------------------------------------------------------------------------- | :-----------------------------------------------------------------------------------------------------------------------: |
| `CONTAINERS_NAMES` | Le nom des conteneurs.                                                                       |                                                          Tableau                                                          |
|   `IMAGES_NAMES`   | Le nom des images qui seront créées à partir des Dockerfile.                                 |                                                          Tableau                                                          |
| `DOCKERFILE_PATH`  | Le chemin vers le dossier contenant le Dockerfile à utiliser pour créé l'image du conteneur. |                                                          Tableau                                                          |
|     `RUN_ARGS`     | Les arguments utilisés lors de la création du conteneur.                                     |   Tableau<br/>(cf. `docker run --help` ou [`Documentation`](https://docs.docker.com/engine/reference/commandline/run/))   |
|    `START_ARGS`    | Les arguments utilisés lors du lancement du conteneur.                                       | Tableau<br/>(cf. `docker start --help` ou [`Documentation`](https://docs.docker.com/engine/reference/commandline/start/)) |
|  `WEBSERVICE_IP`   | L'adresse IP du conteneur du webservice.                                                     |     Chaîne de caractères</br>⚠️ Cette variable est définit automatiquement (cf. [config](./config) lignes 10 à 18). ⚠️      |
| `WEBSERVICE_PORT`  | Le port exposé du conteneur attribué au webservice.                                          |                                                          Entier                                                           |


⚠️&emsp; Les tableaux sont liés par leurs indices c.à.d. que `CONTAINERS_NAMES[0]`, `IMAGES_NAMES[0]`, `DOCKERFILE_PATH[0]`, etc...<br/> seront utilisés pour créer le même conteneur. Tous les paramètres de type tableau doivent donc <u>**IMPÉRATIVEMENT**</u> être de de la même taille ! &emsp;⚠️

<br/>

#### 2.1.2. Variables d'environnement

Voici la liste des variables d'environnement utilisées lors de l'exécution des conteneurs.

|     Paramètre     | Description                                                              |        Format        |
| :---------------: | :----------------------------------------------------------------------- | :------------------: |
|  `WEBSERVICE_IP`  | Définit à l'explorateur de fichiers PHP où se situe le webservice.       | Chaîne de caractères |
| `WEBSERVICE_PORT` | Définit à l'explorateur de fichiers PHP le port du web service.          |        Entier        |
|  `STARTING_DIR`   | Chemin (relatif ou non) définissant le point de départ de l'explorateur. | Chaîne de caractères |
<br/>

### 2.2. Configuration du webservice

|     Paramètre      |                                      Valeur                                      | Explications                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| :----------------: | :------------------------------------------------------------------------------: | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `CONTAINERS_NAMES` |                                   `webservice`                                   | Le nom du conteneur sera `webservice`.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
|   `IMAGES_NAMES`   |                                   `webservice`                                   | Le nom de l'image sera `webservice`.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| `DOCKERFILE_PATH`  |                                 `./webservice/`                                  | Le chemin vers le dossier contenant le Dockerfile pour créér l'image sera [`./webservice/`](./webservice/).                                                                                                                                                                                                                                                                                                                                                                                                                                  |  |
|     `RUN_ARGS`     | `-d -p ${WEBSERVICE_PORT}:8080 --name ${CONTAINERS_NAMES[0]} ${IMAGES_NAMES[0]}` | On souhaite créer un conteneur de manière [détachée](https://docs.docker.com/engine/reference/run/#detached--d). On créé un [mapping de port](https://docs.docker.com/engine/reference/commandline/run/#publish-or-expose-port--p---expose) c.à.d. que l'on ouvre le port `WEBSERVICE_PORT` à l' extérieur du conteneur et on le lie au port `8080` à l'intérieur du conteneur. On y accède donc depuis l'adresse `WEBSERVICE_IP:WEBSERVICE_PORT`. On donne le nom `webservice` au conteneur et on le créé à partir de l'image `webservice`. |
|    `START_ARGS`    |                                                                                  | On ne définit pas de paramètre de lancement. spéciaux.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| `WEBSERVICE_PORT`  |                                      `8080`                                      | On souhaite accéder au serveur Tomcat, depuis l'extérieur, sur le port `8080`.                                                                                                                                                                                                                                                                                                                                                                                                                                                               |
<br/>

### 2.3. Configuration de l'explorateur PHP

La configuration pour l'explorateur de fichiers PHP se fait à la foi par les variables "normales" et par les variables d'environnement.

#### 2.3.1. Configuration du conteneur

|     Paramètre      |                                                                          Valeur                                                                           | Explications                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   |
| :----------------: | :-------------------------------------------------------------------------------------------------------------------------------------------------------: | :----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `CONTAINERS_NAMES` |                                                                     `encode-explorer`                                                                     | Le nom du conteneur sera `encode-explorer`.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    |
|   `IMAGES_NAMES`   |                                                                     `encode-explorer`                                                                     | Le nom de l'image sera `encode-explorer`.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
| `DOCKERFILE_PATH`  |                                                                   `./encode-explorer/`                                                                    | Le chemin vers le dossier contenant le Dockerfile pour créér l'image sera [`./encode-explorer/`](./encode-explorer/)                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           |
|     `RUN_ARGS`     | `-d -p 80:80 ${WEBSERVICE_IP_ARGS}-e WEBSERVICE_PORT=${WEBSERVICE_PORT} -e STARTING_DIR=${STARTING_DIR} --name ${CONTAINERS_NAMES[1]} ${IMAGES_NAMES[1]}` | On souhaite créer un conteneur de manière [détachée](https://docs.docker.com/engine/reference/run/#detached--d). On créé un [mapping de port](https://docs.docker.com/engine/reference/commandline/run/#publish-or-expose-port--p---expose) c.à.d. que l'on ouvre le port `80` à l' extérieur du conteneur et on le lie au port `80` à l'intérieur du conteneur. On y accède donc depuis l'adresse `WEBSERVICE_IP:WEBSERVICE_PORT`. On définit 2 ou 3 variables d'environnement :<br/><ul><li>`WEBSERVICE_IP` si on a pu obtenir l'adresse IP du conteneur du webservice (cf. [config](./config) lignes 10 à 18)</li><li>`WEBSERVICE_PORT` (cf. [2.3.2. Variables d'environnement (facultatif)](#232-variables-denvironnement-facultatif))</li><li>`STARTING_DIR` (cf. [2.3.2. Variables d'environnement (facultatif)](#232-variables-denvironnement-facultatif))</li></ul>On donne le nom `encode-explorer` au conteneur et on le créé à partir de l'image `encode-explorer`. |
|    `START_ARGS`    |                                                                                                                                                           | On ne définit pas de paramètre de lancement.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   |
<br/>

#### 2.3.2. Configuration de l'application

##### 2.3.2.1. Fichiers de configuration

L'application de l'explorateur comporte de nombreux paramètres de configurations répartis dans les fichiers du dossier [encode-explorer/application/config](./encode-explorer/application/config).
Certains peuvent être définis grâce à des variables d'environnement (facultatif). Pour cela, voir la section ci-dessous.
<br/>
##### 2.3.2.2. Variables d'environnement

Il n'est pas obligatoire de définir ces varibales d'environnement.

|     Paramètre     |                                                                             Valeur                                                                             | Explications                                                                                              |
| :---------------: | :------------------------------------------------------------------------------------------------------------------------------------------------------------: | :-------------------------------------------------------------------------------------------------------- |
|  `WEBSERVICE_IP`  | Utilise la variable de configuration `WEBSERVICE_IP` du fichier [`config`](./config) (cf. [2.1.1. Paramètres des conteneurs](#211-paramètres-des-conteneurs))  | Définit où se situe le webservice.                                                                        |
| `WEBSERVICE_PORT` | Utilise la variable de configuration `WEBSERVICE_PORT` du fichier [`config`](./config) (cf. [2.1.1. Paramètres des conteneurs](#211-paramètres-des-conteneurs) | Définit le port du web service.                                                                           |
|  `STARTING_DIR`   |                                                                              `./`                                                                              | On définit le point de départ de l'explorateur comme étant le dossier courrant du contener du webservice. |
<br/>


## 3. Commandes disponnibles

⚠️&emsp; L'utilisation de ces commandes nécessite que [Docker soit installé sur la machine](https://docs.docker.com/engine/install/) ainsi que les privilèges d'administrateur. &emsp;⚠️

| Commande       | Description                                                                                                                             |
| :------------- | :-------------------------------------------------------------------------------------------------------------------------------------- |
| `make build`   | Créé l'image et les conteneurs du tableau `CONTAINERS_NAMES`.                                                                           |
| `make rebuild` | Efface les conteneurs dont le nom est dans le tableau `CONTAINERS_NAMES`. Créé l'image et les conteneurs du tableau `CONTAINERS_NAMES`. |
| `make run`     | Créé les conteneurs du tableau `CONTAINERS_NAMES`.                                                                                      |
| `make start`   | Lance les conteneurs du tableau `CONTAINERS_NAMES`.                                                                                     |
| `make stop`    | Arrête les conteneurs du tableau `CONTAINERS_NAMES`.                                                                                    |
| `make clean`   | Efface les conteneurs dont le nom est dans le tableau `CONTAINERS_NAMES`.                                                               |
<br/>

## 4. Première utilisation

Lors de la première utilisation, se placer dans le dossier `stage-docker` et suivre les instructions ou commandes suivantes :
* `make build` ➔ La 1ère fois, la création des images peut prendre quelques minutes.
* Vous pouvez vérifier que les images ont bien été créées en tapant la commande `sudo docker image ls | grep -E "(encode-explorer)|(webservice)"`.
* Si 2 lignes s'affichent, passez à l'étape suivante sinon lancez la commande `./scripts/build.sh` et analysez les journaux d'exécution.
* `make run`
* Vous pouvez vérifier que les conteneurs ont été créés et qu'ils sont bien en fonctionnement grâce à la commande `sudo docker ps | grep -E "(encode-explorer)|(webservice)"`. Si vous obtenez 2 lignes&ensp; 🎉&ensp; **FÉLICITATIONS !** &ensp;🎉&ensp; L'installation s'est déroulée sans problèmes !
* Si rien ne s'affiche, tapez `sudo docker ps -a | grep -E "(encode-explorer)|(webservice)` et vérifiez que vous obtenez bien 2 lignes.
* Si 2 lignes s'affichent lancez la commande `make start` et réitérez l'étape précédente. Si vous obtenez 2 lignes&ensp; 🎉&ensp; **FÉLICITATIONS !** &ensp;🎉&ensp; L'installation s'est déroulée sans (trop de) problèmes !
* Si vous n'obtenez toujours pas de résultat lancez la commande `./scripts/run.sh` et analysez les journaux d'exécution.

⚠️&emsp; En cas de problème, il est probable qu'il s'agisse d'une mauvaise [configuration](#2-configuration). Pensez à vérifier ! &emsp;⚠️