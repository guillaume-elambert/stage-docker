TP Docker n°1 - Découverte de Docker<!-- omit in toc -->
=============

Ce TP permet de découvrir Docker et ses commandes.


<br/>

## Sommaire<!-- omit in toc -->

- [1. Commandes de Docker](#1-commandes-de-docker)
- [2. Première manipulation d'un conteneur](#2-première-manipulation-dun-conteneur)
  - [2.1. Récupérer une image sur un repository en ligne](#21-récupérer-une-image-sur-un-repository-en-ligne)
  - [2.2. Manipuler les tags](#22-manipuler-les-tags)
  - [2.3. Configuration d'un conteneur](#23-configuration-dun-conteneur)
    - [2.3.1. Configuration en ligne de commande](#231-configuration-en-ligne-de-commande)
    - [2.3.2. Configuration à l'aide d'un Dockerfile](#232-configuration-à-laide-dun-dockerfile)


<br/><br/>


## 1. Commandes de Docker

Docker possède quelques commandes, vous pouvez les retrouver ici : [https://devhints.io/docker](https://devhints.io/docker) ou sur la [Documentation officielle](https://docs.docker.com/reference/) (`Menu déroulant de gauche > Command-line reference > Docker CLI (docker)`).


Mais les commandes principales pour ce TP sont :
```bash
docker [COMMAND] --help # Cette commande permet d'obtenir de l'aide pour une autre commande. Elle est donc TRÈS importante !!
docker pull [OPTIONS] NAME[:TAG|@DIGEST]          # Cette commande permet de télécharger une image disponible sur un registre
docker run [OPTIONS] IMAGE [COMMAND] [ARG...]     # Cette commande permet de créer et lancer un conteneur
docker start [OPTIONS] CONTAINER [CONTAINER...]   # Cette commande permet de lancer un conteneur déjà créé
docker exec [OPTIONS] CONTAINER COMMAND [ARG...]  # Cette commande permet d'executer une autre commande dans le conteneur
docker ps [OPTIONS] # Cette commande renvoie la liste des conteneurs
```

<details markdown="1">
<summary>Liste des commandes (issue de <code>docker --help</code>) </summary>

```
Usage:  docker [OPTIONS] COMMAND

A self-sufficient runtime for containers

Options:
      --config string      Location of client config files (default "/home/guillaume/.docker")
  -c, --context string     Name of the context to use to connect to the daemon (overrides DOCKER_HOST env var and default context set with "docker context use")
  -D, --debug              Enable debug mode
  -H, --host list          Daemon socket(s) to connect to
  -l, --log-level string   Set the logging level ("debug"|"info"|"warn"|"error"|"fatal") (default "info")
      --tls                Use TLS; implied by --tlsverify
      --tlscacert string   Trust certs signed only by this CA (default "/home/guillaume/.docker/ca.pem")
      --tlscert string     Path to TLS certificate file (default "/home/guillaume/.docker/cert.pem")
      --tlskey string      Path to TLS key file (default "/home/guillaume/.docker/key.pem")
      --tlsverify          Use TLS and verify the remote
  -v, --version            Print version information and quit

Management Commands:
  app*        Docker App (Docker Inc., v0.9.1-beta3)
  builder     Manage builds
  buildx*     Build with BuildKit (Docker Inc., v0.5.1-docker)
  config      Manage Docker configs
  container   Manage containers
  context     Manage contexts
  image       Manage images
  manifest    Manage Docker image manifests and manifest lists
  network     Manage networks
  node        Manage Swarm nodes
  plugin      Manage plugins
  scan*       Docker Scan (Docker Inc., v0.8.0)
  secret      Manage Docker secrets
  service     Manage services
  stack       Manage Docker stacks
  swarm       Manage Swarm
  system      Manage Docker
  trust       Manage trust on Docker images
  volume      Manage volumes

Commands:
  attach      Attach local standard input, output, and error streams to a running container
  build       Build an image from a Dockerfile
  commit      Create a new image from a container's changes
  cp          Copy files/folders between a container and the local filesystem
  create      Create a new container
  diff        Inspect changes to files or directories on a container's filesystem
  events      Get real time events from the server
  exec        Run a command in a running container
  export      Export a container's filesystem as a tar archive
  history     Show the history of an image
  images      List images
  import      Import the contents from a tarball to create a filesystem image
  info        Display system-wide information
  inspect     Return low-level information on Docker objects
  kill        Kill one or more running containers
  load        Load an image from a tar archive or STDIN
  login       Log in to a Docker registry
  logout      Log out from a Docker registry
  logs        Fetch the logs of a container
  pause       Pause all processes within one or more containers
  port        List port mappings or a specific mapping for the container
  ps          List containers
  pull        Pull an image or a repository from a registry
  push        Push an image or a repository to a registry
  rename      Rename a container
  restart     Restart one or more containers
  rm          Remove one or more containers
  rmi         Remove one or more images
  run         Run a command in a new container
  save        Save one or more images to a tar archive (streamed to STDOUT by default)
  search      Search the Docker Hub for images
  start       Start one or more stopped containers
  stats       Display a live stream of container(s) resource usage statistics
  stop        Stop one or more running containers
  tag         Create a tag TARGET_IMAGE that refers to SOURCE_IMAGE
  top         Display the running processes of a container
  unpause     Unpause all processes within one or more containers
  update      Update configuration of one or more containers
  version     Show the Docker version information
  wait        Block until one or more containers stop, then print their exit codes

Run 'docker COMMAND --help' for more information on a command.

To get more help with docker, check out our guides at https://docs.docker.com/go/guides/
```
</details>


<br/><br/>


## 2. Première manipulation d'un conteneur
<br/>

### 2.1. Récupérer une image sur un repository en ligne

Docker permet d'utiliser des solutions "prémâchées" que l'on télécharge sur dépôt public.<br/>
En effet il existe de nombreuses images de conteneur disponibles sur le [Docker Hub](https://hub.docker.com/search?type=image). Elles nous permettent de partir d'une base préfaite contenant certaines librairies et d'y ajouter ce que l'on veut.<br/>

Nous allons alors créé notre premier conteneur basé sur PHP.<br/>


>**Pour cela, chercher l'image de PHP sur le Docker Hub et la télécharger.**

<details markdown="1">
<summary>Solution </summary>


```bash
$ docker pull php # Cette commande télécharge l'image PHP depuis le Docker Hub
```
</details>

<br/>


>**Une fois cela fait nous pouvons créer le conteneur PHP.**

<details markdown="1" id="remarque-docker-run">
<summary>Solution & remarque</summary>

**Solution :**

```bash
$ docker run php  # Cette commande créé et lance un conteneur PHP
```


**Remarque :**
Lorque l'on exécute la commande `docker run nom-de-l-image` sans avoir téléchargé l'image au préalable, Docker se charge de le faire pour nous.
</details>

<br/>


>**Vérifier que le conteneur à bien été créé et qu'il est en cours d'éxecution.**

<details markdown="1">
<summary>Solution </summary>


```bash
$ docker ps     # Rien ne s'affiche, le conteneur n'est pas en cours d'éxecution
$ docker ps -a  # On retrouve bien notre conteneur, il est créé mais arrêté
```
</details>

<br/>



>**Pourquoi le conteneur est-il arrêté ?**


C'est assez simple. Un conteneur s'exécute tant qu'un processus est en cours d'exécution puis il s'arrête dès la fin de tous les processus.<br/>
Or ici nous n'avons que PHP dans le conteneur. Ce n'est pas un processus qui tourne de manière permanente, il est normalement utilisé par un serveur WEB tel qu'Apache ou NGINX qui va quant à lui tourner de manière continue et donc maintenir le conteneur actif.<br/>
Ainsi, n'ayant pas de processus en cours d'éxecution le conteneur s'est arrêté.


<br/>


### 2.2. Manipuler les tags

<br/>

----------

>**<u>Note :</u>**<br/>

Pour éviter d'avoir de nombreux conteneurs inutiles je conseille de fréquemment utiliser la commande :
```bash
# Supprime l'ensemble des conteneurs
$ docker rm -f `docker ps -aq`

# Ou si vous avez d'autres conteneurs que vous voulez conserver

# Supprime l'ensemble des conteneurs basés sur l'image php:apache
$ docker rm -f `docker ps -aq --filter "ancestor=php:apache"`
```
----------


<br/>

>**Comment résoudre ce problème ?**


Comme vous l'aurez compris il manque Apache, donc il suffit de l'installer. Facile ! On suit [la documentation d'Apache](https://httpd.apache.org/docs/2.4/fr/install.html) et Hop c'est fait ! 👍💪<br/>
On se rend vite compte que ce n'est pas si facile que ça finalement... et puis surtout ça demande du temps !<br/>
Pas de panique il existe une solution "prémâchéee".<br/>
En effet les images Docker possèdent des `tags` qui permettent par exemple de définir la version de l'image que nous souhaitons. Ne serait-ce pas formidable s'il existait une version de l'image PHP qui contient Apache dedans ? 🤯😲

Un peu de documentation sur les tags Docker : [https://www.freecodecamp.org/news/an-introduction-to-docker-tags-9b5395636c2a/](https://www.freecodecamp.org/news/an-introduction-to-docker-tags-9b5395636c2a/)


<br/>

>**Parcourir [la page Docker Hub de PHP](https://hub.docker.com/_/php) et créer un conteneur basé sur la dernière version d'Apache.**


<details markdown="1">
<summary>Solution </summary>

**Rappel :** La première ligne est facultative (cf. [Remarque](#remarque-docker-run)).


```bash
# On télécharge l'image de PHP qui contient Apache
# Ici on ne spécifie ni la version de PHP ni celle d'Apache dans le tag
# Donc par défaut c'est la dernière version qui sera choisie
$ docker pull php:apache
$ docker run php:apache
```
</details>

<br/>


>**Ouvrir un nouveau terminal et vérifier que le conteneur à bien été créé et qu'il est en cours d'éxecution.**

<details markdown="1">
<summary>Solution </summary>


```bash
$ docker ps     # Liste les conteneurs en cours d'exécution
$ docker ps -a  # Liste les conteneurs arrêtés
```



Normalement, le conteneur devrait soit s'arrêter immédiatement, soit fonctionner un temps et finir par s'arrêter. Dans un cas comme dans l'autre nous obtenons des journaux d'exécution d'Apache.<br/>
Si vous êtes dans le 2ème cas faites `CTRL+C` pour forcer l'arrêt d'Apache et du conteneur puis passer à la suite.
</details>

<br/>



>**Interprettons les logs d'Apache.**


On obtient quelque chose de la sorte :

```
AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.17.0.2. Set the 'ServerName' directive globally to suppress this message
AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.17.0.2. Set the 'ServerName' directive globally to suppress this message
[Tue Jul 13 09:07:39.355136 2021] [mpm_prefork:notice] [pid 1] AH00163: Apache/2.4.38 (Debian) PHP/8.0.8 configured -- resuming normal operations
[Tue Jul 13 09:07:39.355572 2021] [core:notice] [pid 1] AH00094: Command line: 'apache2 -D FOREGROUND'
[Tue Jul 13 09:07:56.122761 2021] [mpm_prefork:notice] [pid 1] AH00170: caught SIGWINCH, shutting down gracefully
```

Les 2 premières lignes nous intéressent peu car elles indiquent un manque de configuration du nom de domaine ce qui ne nous concerne pas.<br/>
Les 3ème et 4ème lignes indiquent simplement le début de la configuration d'Apache.<br/>
La dernière ligne est assez intéressante. On peut voir que le signal `WINCH` a été renvoyé lors de l'exécution d'Apache or d'après [la documentation d'Apache](https://httpd.apache.org/docs/2.4/stopping.html#gracefulstop) cela correspond à une demande d'arrêt du programme.<br/>
On peut se demander ce qu'il s'est passé pour que cela arrive.


<br/>

>**Pourquoi Apache (et donc le conteneur) s'est-il arrêté ?**


Cela vient du fait qu'Apache a besoin que nous lui attribuions un port pour s'exécuter et que nous lui fournissions du contenu à afficher.


<br/>


### 2.3. Configuration d'un conteneur

#### 2.3.1. Configuration en ligne de commande

>**Attribuer un port à un conteneur.**


D'après la commande `docker run --help` il existe une option : `-p` décrite de la sorte : `Publish a container's port(s) to the host` qui correpond à nos besoin.<br/>
En ajoutant cette option à la commande `run` initiale on obtient :
```bash
docker run -p QUELQUECHOSE php:apache
```

Mais comment formuler le `QUELQUECHOSE` ?<br/>
L'attribution de port dansDocker se fait de la sorte :
```bash
docker run -p PORT_HOTE:PORT_CONTENEUR IMAGE
```

Le premier nombre `PORT_HOTE` correspond au port de la machine hôte (ici votre PC) qui sera attribué au conteneur.<br/>
Le second nombre `PORT_CONTENEUR` correspond au port du conteneur qui sera exposé.<br/>
On lie donc le port `PORT_CONTENEUR` avec le port `PORT_HOTE`.<br/>

(cf. [https://docs.docker.com/engine/reference/run/#expose-incoming-ports](https://docs.docker.com/engine/reference/run/#expose-incoming-ports))

<br/>


>**Créons un conteneur Apache PHP. Exposer son port 80 et le lier au port 8080 de notre machine.**


```bash
docker run -p 8080:80 php:apache
```

On peut maintenant accéder au serveur Apache via [http://localhost:8080/](http://localhost:8080/) cependant il n'y a pas de contenu.<br/>
Nous pouvons ajouter une page d'accueil en "bidouillant" un peu. Pour cela on utilise les commandes suivantes :
```bash
# Rappel : Supprime l'ensemble des conteneurs basés sur l'image php:apache
$ docker rm -f `docker ps -aq --filter "ancestor=php:apache"`

# On créé un conteneur Apache avec PHP
# On définit le fait que le port 8080 de notre machine sera lié au port 80 du conteneur
# On passe une commande au conteneur qui :
#   1- Lance bash et exécute la chaîne de caractère comme une commande
#   2- Écrit "Hello World ! <?php phpinfo(); ?>" dans le fichier /var/www/html/index.php du conteneur
#   3- Lance Apache
docker run -d -p 8080:80 php:apache bash -c "echo \"Hello World ! <?php phpinfo(); ?>\" > index.php; apache2-foreground"
```

Désormais, lorsqu'on accède à [http://localhost:8080/](http://localhost:8080/), on obtient une page de ce style :
![first-index-creation-apache-docker](./first-index-creation-apache-docker.png)

<br/>

Le résultat nous montre bien que nous avons réussi cependant vous imaginez-vous mettre au point une commande aussi compliquée que celle précédente pour un projet contenant 100 ou 200 fichiers ?<br/>
Bien sûr que non ! Heureusement il existe une solution, le Dockerfile.

Nous aborderons les Dockerfiles dans le TP suivant : [TP Docker n°2 - Découverte des Dockerfile](../TP2)