TP Docker n°2 - Découverte des Dockerfile<!-- omit in toc -->
=============

Dans ce TP nous aborderons une notion primoridale de Docker : la configuration d'une image grâce au Dockerfile.<br/>
Je n'aborderait pas toutes les subtilités du Dockerfile donc je vous invite à parcourir la [documentation officielle](https://docs.docker.com/engine/reference/builder/) tout au long de ce TP pour en découvrir d'avantage.


<br/>


## Sommaire<!-- omit in toc -->

- [1. Introduction au Dockerfile](#1-introduction-au-dockerfile)
- [2. Utilisation d'une image pré-existante](#2-utilisation-dune-image-pré-existante)
- [3. Définir des ports](#3-définir-des-ports)
- [4. Copier des fichiers dans le conteneur](#4-copier-des-fichiers-dans-le-conteneur)


<br/>


## 1. Introduction au Dockerfile

Un Dockerfile est comme son nom l'indique un fichier utilisé par Docker pour générer une image.<br/>
Docker lit les instructions de ce fichier et construit notre image personnalisée grâceà la commande :
```bash
docker build -t NOM_IMAGE CHEMIN_DOCKERFILE
```

De là nous pouvons utiliser cette image pour créer nos conteneurs.<br/>


<br/>


## 2. Utilisation d'une image pré-existante

Tout comme avec la commande `docker run`, on peut utiliser une image disponible sur le [Docker Hub](https://hub.docker.com/search?type=image) pour nous faciliter le travail.<br/>
Pour cela il suffit de :

>**Télécharger le projet [https://github.com/guillaume-elambert/stage-docker](https://github.com/guillaume-elambert/stage-docker).**


<br/>

>**Se placer dans le dossier "encode-explorer" et supprimer le contenu du fichier nommé "Dockerfile".**


<br/>

>**Saisir le contenu suivant dans le Dockerfile.**

```dockerfile
FROM image
```

Donc dans notre cas
```dockerfile
FROM php:apache
```

Jusqu'ici nous pouvons déduire que le résultat sera le même qu'avec la commande vue dans le TP précédent. Pour rappel elle permet de créer un conteneur où Apache et PHP sont installés mais où il n'y ni contenu ni configuration pour le serveur Apache.
```bash
docker run php:apache
```

>**Construire l'image grâce au Dockerfile.**

<details markdown="1">
<summary>Solution </summary>

En admettant que vous vous trouvez dans le dossier "encode-explorer", la commande pour construire l'image est :

```bash
# On créé l'image nommée "encode-explorer" à partir du Dockerfile contenu dans le fichier courrant
$ docker build -t encode-explorer .
```
</details>


<br/>


>**Créer le conteneur basé sur l'image créée.**

<details markdown="1">
<summary>Solution </summary>

La commande pour créer le conteneur basé sur notre image "encode-explorer" est :

```bash
$ docker run encode-explorer
```
</details>


<br/>


## 3. Définir des ports

Pour rappel nous avons vu que le conteneur Apache à besoin que nous lui fournissions un port.<br/>
Nous le faisions avec la commande :
```bash
docker run -p 8080:80 php:apache
```
Ainsi nous obtenions un conteneur configuré avec notre serveur Apache et nous pouvions y accéder via [http://localhost:8080/](http://localhost:8080/).


**<u>Le Dockerfile ne permet pas se passer de l'option `-p PORT_HOTE:PORT_CONTENEUR`</u>** mais seulement de signaler que le conteneur à besoin que nous lui attribuions un port. Cette partie sert donc de documentation pour les utilisateurs.<br/>
Pour cela nous remplaçons le contenu de notre Dockerfile par :
```dockerfile
FROM php:apache
EXPOSE 80
```

Comme vous pouvez le constater il n'est question ici que d'un seul port.<br/>
En effet on ne définit dans le Dockerfile que le port du conteneur que l'on ouvre. Ainsi les utilisateurs sont libres de choisir quel port de leur machine sera lié au port 80 du conteneur.<br/>

<br/>

Donc il sera <u>**toujours**</u> nécessaire de spécifier l'option `-p PORT_HOTE:PORT_CONTENEUR` mais l'utilisateur peut consulter la liste des ports dont le conteneur à besoin et choisir librement comment il les réparti sur sa machine.<br/>
Je vous invite à consulter [la documentation officielle](https://docs.docker.com/engine/reference/builder/#expose) pour de plus amples informations.


<br/>


## 4. Copier des fichiers dans le conteneur

Dans le TP précédent nous avions réussi à créer la page d'accueil de notre serveur en "bidouillant" une commande à exécuter lors du lancement de notre conteneur :
```bash
# On créé un conteneur Apache avec PHP
# On définit le fait que le port 8080 de notre machine sera lié au port 80 du conteneur
# On passe une commande au conteneur qui :
#   1- Lance bash et exécute la chaîne de caractère comme une commande
#   2- Écrit "Hello World ! <?php phpinfo(); ?>" dans le fichier /var/www/html/index.php du conteneur
#   3- Lance Apache
docker run -d -p 8080:80 php:apache bash -c "echo \"Hello World ! <?php phpinfo(); ?>\" > index.php; apache2-foreground"
```

Le problème ici est que, en plus d'être très barbare, cette manière de faire ne permet pas de copier ou créer facilement tout un projet dans le conteneur.<br/>
Pour pallier cela, le Dockerfile peut contenir l'instruction [`COPY`](https://docs.docker.com/engine/reference/builder/#copy).<br/>
Ainsi pour copier le contenu d'un dossier "/chemin/vers/mon/dossier/source" on obtient le Dockerfile suivant :
```dockerfile
FROM php:apache
EXPOSE 80
COPY /chemin/vers/mon/dossier/source destination/dans/le/conteneur
```

Dans notre cas :
```dockerfile
FROM php:apache
EXPOSE 80
COPY /chemin/vers/le/dossier/encode-explorer /var/www/html
```

Ou avec des chemins relatifs. Si le fichiers Dockerfile se trouve dans le dossier "encode-explorer", on obtient :
```dockerfile
FROM php:apache
EXPOSE 80
COPY . .
```

----------

>**<u>Note :</u>**<br/>

Le premier point est le chemin vers le dossier courrant. <br/>
Le second correspond au dossier `/var/www/html`. En effet l'image `php:apache` définit le point de départ du conteneur comme étant `/var/www/html`. Ainsi toutes les commandes que nous lui passons sont effectuées relativement à ce dossier.<br/>
Pour plus d'informations, [se renseigner sur l'instruction `WORKDIR`](https://docs.docker.com/engine/reference/builder/#workdir) et observer [le contenu du Dockerfile de l'image `php:apache`](https://hub.docker.com/layers/php/library/php/apache/images/sha256-0421c31c13f932a99c4bede6ca065a4fccf97f193b4c1f28db327dc405456622?context=explore).

----------

<br/>

>**Supprimer tous les conteneurs grâce à la commande suivante.**

```bash
# Supprime l'ensemble des conteneurs
$ docker rm `docker ps -aq`
```

<br/>


>**Construire l'image grâce au Dockerfile puis créer un nouveau conteneur que vous nommerez "encode-explorer" basé sur celle-ci.**

<details markdown="1">
<summary>Solution </summary>

En admettant que vous vous trouvez dans le dossier "encode-explorer" les commandes sont :

```bash
# On créé l'image nommée "encode-explorer" à partir du Dockerfile contenu dans le fichier courrant
$ docker build -t encode-explorer .

# Cette commande permet de créer un conteneur basé sur l'image "encode-explorer".
# On lit le port 8080 de notre machine avec le port 80 du conteneur.
# On lui donne le nom "encode-explorer".
# Le nommage des conteneurs permet de faciliter leur manipulation.
$ docker run -p 8080:80 encode-explorer --name encode-explorer
```

<br/>

⚠️&emsp; **<u> ATTENTION </u>** &emsp;⚠️<br/>
Pour rappel il est toujours nécessaire de spécifier l'attribution de port lors de la création du conteneur, même si nous l'avons spécifier dans le Dockerfile.<br/>
Ne pas oublier !


</details>


<br/>


>**Vérifier que nous avons bien les fichiers dans notre conteneur.**

Pour cela il faut utiliser la commande `docker exec` (voir [la documentation officielle](https://docs.docker.com/engine/reference/commandline/exec/) ou `docker exec --help`).<br/>
Plusieurs options s'offrent à nous pour vérifier que nous obtenons bien le résultat escompté :
1. Nous spécifions une commande pour lister les fichiers telle que `ls`
2. Nous spécifions une commande qui nous permet de rentrer dans le conteneur et de le manipuler grâce à un terminal.

<br/>

Pour la première solution on utilise la commande :
```bash
# On exécute la commande "ls -l" sur le conteneur nommé "encode-explorer".
# L'option "-t" permet de retourner visuellement le résultat de la commande.
$ docker exec -t encode-explorer ls -l
```

<br/>

Pour la seconde solution :
```bash
# On exécute la commande "bash" sur le conteneur nommé "encode-explorer" (c.à.d qu'on lance un terminal).
# L'option "-i" permet de définir que nous allons interagir avec le conteneur (ici via le terminal bash).
# L'option "-t" permet de retourner visuellement le résultat des commandes passées au sein du conteneur.
$ docker exec -i -t encode-explorer bash

# Un terminal se lance.
# Vous remarquerez que comme vous êtes dans le conteneur vous avez des identifiants différents.
# Vous remarquerez également que nous sommes bien dans le dossiers /var/www/html comme vu un peu plus haut.

# On peut désormait lancer notre commande "ls -l"
root@e071acc58f08:/var/www/html$ ls -l

# Puis on sort du conteneur grâce à la commande "exit"
root@e071acc58f08:/var/www/html$ exit
```

<br/>

Après avoir réalisé l'une des deux solutions présentées nous pouvons constater que nous obtenons bel et bien un conteneur qui contient les fichiers de notre dossier "encode-explorer".