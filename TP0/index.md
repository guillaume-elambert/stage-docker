TP Docker n°0 - Installer Docker sur Linux<!-- omit in toc -->
=============

Ce TP a pour seul but d'installer Docker et vérifier que l'installation a bien été faite.


<br/>


## Sommaire<!-- omit in toc -->

- [1. Installation](#1-installation)
- [2. Utiliser Docker sans les droits de super utilisateur](#2-utiliser-docker-sans-les-droits-de-super-utilisateur)
- [3. Vérification de l'installation](#3-vérification-de-linstallation)


<br/><br/>


## 1. Installation

<br/>

----------

>**<u>Note :</u>**<br/>

Dans cette partie je montrerai comment installer Docker sur les distributions basées sur Ubuntu. Pour toute autre distribution se référer au [site de Docker](https://docs.docker.com/engine/install/) (`Menu déroulant de gauche > Installation per distro`).

----------


<br/>

D'après la [documentation officielle](https://docs.docker.com/engine/install/ubuntu/#install-using-the-repository), les commandes à effectuer pour installer Docker sont :

```bash
$ sudo apt-get update

$ sudo apt-get install \
       apt-transport-https \
       ca-certificates \
       curl \
       gnupg \
       lsb-release

$ curl -fsSL https://download.docker.com/linux/ubuntu/gpg | \
  sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

$ echo \
  "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

$ sudo apt-get update

$ sudo apt-get install docker-ce docker-ce-cli containerd.io
```


<br/><br/>


## 2. Utiliser Docker sans les droits de super utilisateur
<br/>

Facultativement, on peut faire en sorte d'utiliser les commandes docker sans être un super utilisateur :


```bash
$ sudo groupadd docker

$ sudo usermod −aG docker $USER

# Après avoir ajouté l'utilisateur au groupe docker, se déconnecter et se reconnecter
# pour que les changements soient pris compte (un redémarrage peut être nécessaire).
# Ou utiliser les commandes :
$ newgrp docker
$ sudo service docker restart
```

Pour vérifier que cela à fonctionné, tapper la commande :
```bash
$ docker version
```

Le résultat devrait être similaire à ce qui suit :

```
Client: Docker Engine - Community
 Version:           20.10.7
 API version:       1.41
 Go version:        go1.13.15
 Git commit:        f0df350
 Built:             Wed Jun  2 11:56:41 2021
 OS/Arch:           linux/amd64
 Context:           default
 Experimental:      true

Server: Docker Engine - Community
 Engine:
  Version:          20.10.7
  API version:      1.41 (minimum version 1.12)
  Go version:       go1.13.15
  Git commit:       b0f5bc3
  Built:            Wed Jun  2 11:54:53 2021
  OS/Arch:          linux/amd64
  Experimental:     false
 containerd:
  Version:          1.4.6
  GitCommit:        d71fcd7d8303cbf684402823e425e9dd2e99285d
 runc:
  Version:          1.0.0-rc95
  GitCommit:        b9ee9c6314599f1b4a7f497e1f1f856fe433d3b7
 docker-init:
  Version:          0.19.0
  GitCommit:        de40ad0
```


<br/><br/>


## 3. Vérification de l'installation
<br/>

Pour vérifier que l'installation s'est bien déroulée, simplement saisir la commande :
```bash
$ sudo docker run hello-world
```

Vous devriez obtenir un résultat similaire à ce qui suit :
```
Hello from Docker!
This message shows that your installation appears to be working correctly.

To generate this message, Docker took the following steps:
 1. The Docker client contacted the Docker daemon.
 2. The Docker daemon pulled the "hello-world" image from the Docker Hub.
    (amd64)
 3. The Docker daemon created a new container from that image which runs the
    executable that produces the output you are currently reading.
 4. The Docker daemon streamed that output to the Docker client, which sent it
    to your terminal.

To try something more ambitious, you can run an Ubuntu container with:
 $ docker run -it ubuntu bash

Share images, automate workflows, and more with a free Docker ID:
 https://hub.docker.com/

For more examples and ideas, visit:
 https://docs.docker.com/get-started/
```


<br/>

Pour repartir d'une installation comme neuve :
```bash
# Supprime l'ensemble des conteneurs
$ docker rm `docker ps -aq`

# Supprime l'ensemble des images
$ docker rmi -f `docker image ls -q`
```