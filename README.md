# Teaching-HEIGVD-RES-2018-Labo-HTTPInfra

## Objectives

The first objective of this lab is to get familiar with software tools that will allow us to build a **complete web infrastructure**. By that, we mean that we will build an environment that will allow us to serve **static and dynamic content** to web browsers. To do that, we will see that the **apache httpd server** can act both as a **HTTP server** and as a **reverse proxy**. We will also see that **express.js** is a JavaScript framework that makes it very easy to write dynamic web apps.

The second objective is to implement a simple, yet complete, **dynamic web application**. We will create **HTML**, **CSS** and **JavaScript** assets that will be served to the browsers and presented to the users. The JavaScript code executed in the browser will issue asynchronous HTTP requests to our web infrastructure (**AJAX requests**) and fetch content generated dynamically.

The third objective is to practice our usage of **Docker**. All the components of the web infrastructure will be packaged in custom Docker images (we will create at least 3 different images).


## Step 1: Static HTTP server with apache httpd

### Objective
In this first part we create a web static page that can be contained in a docker.

### Manipulation
You can find the dockerFile in staticWebPage folder and the web ressource in the folder staticWebPage/src.
The template was found in https://startbootstrap.com/template-overviews/agency/.
I modified the index.html file to personalize the website. Added my own picture in the folder img/team.
I used the php:7.0-apache and not the apache official docker because php is ever a must have in website construction.
Notice that the docker apache need by default to contains the website in /var/www/html/ that's why we use COPY src/ /var/www/html/.

### Webcasts

* [Labo HTTP (1): Serveur apache httpd "dockerisé" servant du contenu statique](https://www.youtube.com/watch?v=XFO4OmcfI3U)

### Tests

it's possible to build and run the docker and check in a browser that we can visualize the website :
In my configuration i can run the docker terminal then move to my git repos (the path change depending on your own configuration):
cd D:/Semestre4/RES/Labo2/Teaching-HEIGVD-RES-2018-Labo-HTTPInfra
Then build and run the docker :
docker build -t static ./staticWebPage
docker run -d -p 8080:80 --name static static
and visualize the website in a browser at : 192.168.99.100:80

It's possible to see the website files with exec :
docker exec -it static bash
ls
You will find the same structure than in the folder staticWebPage/src

## Step 2: Dynamic HTTP server with express.js

### Objective

Here we want to create a node application that can be contained in a docker. I choose to do an application that return a random list of animals name in a json payload.

### Manipulation

You can find all the resources in the folder dynamicWebPage.
There is a DockerFile and the src that contains necessary ressource to run the application.
I choose the node:8.11.1 Docker base image and the main working directory is in /opt/app/ that's why we use COPY src /opt/app/
Then we immediately run the applicationy after a docker run with CMD ["node", "/opt/app/index.js"]
index.js is then the application and can be found in the src folder.
The application code simply listen any http request on port 3000 and return a random list of animals name.
To provide this functionnality I used two modules : Chance and Express
It's necessary to install them before trying to launch it. We don't provide the modules in the github because those kind of dependences are heavy but easy to install.

### Webcasts

* [Labo HTTP (2a): Application node "dockerisée"](https://www.youtube.com/watch?v=fSIrZ0Mmpis)
* [Labo HTTP (2b): Application express "dockerisée"](https://www.youtube.com/watch?v=o4qHbf_vMu0)

### Tests

First you need to install the missing modules in the src :
cd D:/Semestre4/RES/Labo2/Teaching-HEIGVD-RES-2018-Labo-HTTPInfra/dynamicWebPage/src
npm init
and answer the questions of the terminale, then
npm install --save chance
npm install --save express
It will install a package node_modules and install the necessary modules into it.

After that you can move back :
cd D:/Semestre4/RES/Labo2/Teaching-HEIGVD-RES-2018-Labo-HTTPInfra
and build and run the docker application :
docker build -t dynamic ./dynamicWebPage
docker run -d -p 8080:3000 --name dynamic dynamic

You can finally visualize the result in your browser at http://192.168.99.100:8080/
You should see the json payload with the random list of animals. If you refresh the page the list should change.

remark : As the different parts of this lab listen in the same port, they can interfer each others, you should clean your docker between each part with :
docker kill $(docker ps -q)    to stop the containers
docker rm $(docker ps -a -q)   to remove the containers
docker rmi $(docker images -q) to remove the images

## Step 3: Reverse proxy with apache (static configuration)

### Objective
As we want the infrastructure to be clean, the servers shouldn't be accessed immediately. Thats why in this step we want a reverse proxy that will be the only entrypoints from extern http request. The proxy will decide himself, wich container has to be accessed with the request path. In our case / should be the static website and /api/animals/ should be the dynamic node container.

### Manipulation

The proxy's files can be found in the folder proxy. However, I kept on github only the dynamical solution, that has the base, that's why there is some more files than necessary for this step.
The static solution needed only the DockerFile and the conf folder.
We used the php:7.0-apache docker image wich provide all we need to configure the proxy behaviour of the container.


To configure the proxy we need to activate two module proxy and proxy_http. We need to run a2enmod proxy proxy_http to activate them. So the DockerFile contain RUN a2enmod proxy proxy_http to activate them when the container is started. You can see the full list of modules with a2enmod.

Another point is to activate the (web)sites. That's why we added RUN a2ensite 000-* 001-* wich means that if the website is found in the resources, we use the configuration proxy file 001-* (the file starting with 001-) to route the request. If the website is not found we use the 000-* file. For greater comprehension of the command a2ensite you can read : https://manpages.debian.org/jessie/apache2/a2ensite.8.en.html.

The last thing to do is to provide the two files of configuration (000-default.conf and 001-reverse-proxy.conf). You can find them in the proxy/conf/sites-available folder. We copy the conf folder in the container with : COPY conf/ /etc/apache2
In the configuration file you need to provide a ProxyPass and a ProxyPassReverse for each possible url. So that the proxy knows how to redirect the incoming and outgoing requests. Be carefull to respect the synthaxe of this kind of file.

It's in this step that we decide to give an alias to the 192.168.99.100 ip adress. Then we modify the Hosts file. On windows 10 you can find him in : C:\Windows\System32\drivers\etc and add a line : 192.168.99.100      demo.res.ch

### Webcasts

* [Labo HTTP (3a): reverse proxy apache httpd dans Docker](https://www.youtube.com/watch?v=WHFlWdcvZtk)
* [Labo HTTP (3b): reverse proxy apache httpd dans Docker](https://www.youtube.com/watch?v=fkPwHyQUiVs)
* [Labo HTTP (3c): reverse proxy apache httpd dans Docker](https://www.youtube.com/watch?v=UmiYS_ObJxY)

### Tests

You can't test directly from my github code the static proxy because I kept only the dynamic solution (because the static solution is weak and dependent of the number of running containers). However if you followed my manipulations you just need to build and run the 3 dockers and try to access in a browser with demo.res.ch/:8080 or demo.res.ch/api/animals/:8080. Just be sure that the ip adress given by docker correspond to what you put in the configuariont file. You can check it with the command : 
docker inspect static | grep -i ipaddr 
where static is the name of the container you want to check.

## Step 4: AJAX requests with JQuery

### Objective

Here we want our static website to change and refresh his contain periodically. This functionality can be done with an JAX request. Fortunately, as we have done a reverse proxy, the content that we ask to the static server and the refreshed content that come from the dynamic node application server appear for the browser to come from the same origin. It means that we won't have any trouble with the "same-origin policy" rule. That rule mandatory all the script's request that come from a domain to be sent to this domain and not any other. Here the only known domain are the proxy.

### Manipulation

First I decided to dynamically change this text on the index.html file : <div class="intro-heading text-uppercase">Salut</div>. I keep in mind the class of this text that will be the way i will access her in the js script. But the most important is to indicate at the end the script to run with <script src="js/animals.js"></script>. Here when the html is red, we launch the animals.js script that is located in the folder js.
Then let's add the script in the corresponding folder. In this script we define a function that will be called when JQuery module as finish to load $(function() {};. the dollar is the key point for it. In this function we define a function loadAnimals and call it each 2 second with setInterval(loadAnimals, 2000);. The loadAnimals function will simply call an http request to the dynamic server (url /api/animals/) get the first animal name in the list and replace the intro-heading text-uppercase class's text by this name. 
It will result to a dynamical containt html.

### Webcasts

* [Labo HTTP (4): AJAX avec JQuery](https://www.youtube.com/watch?v=fgpNEbgdm5k)

### Tests

You can directly see the result by launching the 3 server (static, dynamic and proxy and visualize the result at demo.res.ch:8080) if you took the code from my github and did the npm install commands of step 2. Notice that i have done step 5 before step 4 that's why I provide the environment variables yet.
Here are the commands necessary for my own computer :
Build the images :
docker build -t static ./staticWebPage
docker build -t dynamic ./dynamicWebPage
docker build -t proxy ./proxy
Run the containers :
docker run -d --name static static
docker run -d --name dynamic dynamic
docker run -d -e STATIC_APP=172.17.0.2:80 -e DYNAMIC_APP=172.17.0.3:3000 --name proxy -p 8080:80 proxy
demo.res.ch:8080

Take care sometime with windows, apache2-foreground is save with windows endline, but docker needs unix endline. So if the proxy dies too fast it's probably the problem. You can check his state with docker ps -all.

## Step 5: Dynamic reverse proxy configuration

### Objective

Now we want to get a stronger ip mapping between the proxy and the servers. Wich means no more hardcoding. We want to privide two environment variable to the proxy docker when we run it. And the proxy should adapt his configuration considering them.

### Manipulation

Here we Used a php file to build a template that will replace the 001-reverse-proxy.conf file defined in step 3. The php file read the enviroment variables with $staticApp = getenv('STATIC_APP');. We read the STATIC_APP environment and put his containt in the variable $staticApp. Then we can use those variables to build the file exactly with the same fomrat that previously, but the ipadress and ports will depends on the given variable environment.
After that we need to tell the image that he has to replace the file. If we take a look to the docker image that i have choose for the proxy (php:7.0-apache), we can find his github (https://github.com/docker-library/php/blob/ddc7084c8a78ea12f0cfdceff7d03c5a530b787e/7.0/apache/Dockerfile). They use the COPY apache2-foreground /usr/local/bin/ and CMD ["apache2-foreground"]. Our method will be to add a functionality to the file apache2-foreground. we copy it from github and add the line php /var/apache2/template/config-template.php > /etc/apache2/sites-available/001-reverse-proxy.conf So that the php is run and his result replace the target file.
Now it should work, you just need to know that in a docker run command, the -e flag defines an enviroment variable. It means that we can define -e STATIC_APP=172.17.0.2:80. Carefull don't put any space before and after '='.

### Webcasts

* [Labo HTTP (5a): configuration dynamique du reverse proxy](https://www.youtube.com/watch?v=iGl3Y27AewU)
* [Labo HTTP (5b): configuration dynamique du reverse proxy](https://www.youtube.com/watch?v=lVWLdB3y-4I)
* [Labo HTTP (5c): configuration dynamique du reverse proxy](https://www.youtube.com/watch?v=MQj-FzD-0mE)
* [Labo HTTP (5d): configuration dynamique du reverse proxy](https://www.youtube.com/watch?v=B_JpYtxoO_E)
* [Labo HTTP (5e): configuration dynamique du reverse proxy](https://www.youtube.com/watch?v=dz6GLoGou9k)

### Tests

you can use exactly the same test than step 4.

## Load balancing: multiple server nodes

### Objective

Now we want to be able to lauch several static and dynamic server. The proxy should be able to distribute the requests between these nodes.

### Manipulation

First you need to enable the two modules in the proxy dockerfile proxy_balancer and lbmethod_byrequests. With this new modules, the proxy will understand the new balises that follow.
Then write in the template file <Proxy balancer://staticapp> that define a "group of load balancing" called staticapp. 
To add a member to this group, just put between the balises BalancerMember "http://<?php print "$staticApp1"?>"
And add another load balancing group for the dynamics servers.

### Tests

The code is situated in the bonus1 branch.
You can run 2 static server, 2 dynamic server and the proxy :
cd D:/Semestre4/RES/Labo2/Teaching-HEIGVD-RES-2018-Labo-HTTPInfra
Build the images :
docker build -t static ./staticWebPage
docker build -t dynamic ./dynamicWebPage
docker build -t proxy ./proxy

Run the containers :
docker run -d --name static1 static
docker run -d --name static2 static
docker run -d --name dynamic1 dynamic
docker run -d --name dynamic2 dynamic
docker run -p 8080:80 -e STATIC_APP1=172.17.0.2:80 -e STATIC_APP2=172.17.0.3:80 -e DYNAMIC_APP1=172.17.0.4:3000 -e DYNAMIC_APP2=172.17.0.5:3000 proxy

visualize the result in the browser :
demo.res.ch:8080

remark : As we can see, if the number of servers increase, longer the docker run command is. That's why we will use a proxy more evoluated in the next bonus.

## Load balancing: round-robin vs sticky sessions

### Objective

Now we want the algorithm of request's distribution to be a round-robin or a sticky session. Fortunately, Traefik is a proxy that provide those two functionality.

### Manipulation

To configure Traefik, I created a new folder called proxyBalancing. With a DockerFile and a configuration file. You can find the complete documentation on how to provide the configuration file of traefik at https://docs.traefik.io/v1.0/toml/. However, I kept the strict necessary in the file to not be lost, because it's a powerfull tool with many options. The DockerFile does simply get the traefik docker base and copy the traefik.toml configuration file into /etc/traefik/ of the futur docker container.
The last thing to do, is to give "LABELS" to our dockers servers. Each label can override the default behaviour of traefik managing them.
There is the example for the dynamic server :
LABEL "traefik.backend"="express-image"
LABEL "traefik.port"="3000"
LABEL "traefik.frontend.rule"="PathPrefixStrip: /api/animals/"
LABEL "traefik.backend.loadbalancer.sticky"="false"

Notice that traefik.backend.loadbalancer.sticky is a sticky session as true, and a round-robin as false. (Here is the documentation : https://docs.traefik.io/configuration/backends/docker/#on-containers) Carefull this method is deprecated, You should find a new way to specify the load balancer algorithm.

### Tests

The two next step are in the bonus2 branch.
you can build and run the pool as usual :
Build the images : 
docker build -t static ./staticWebPage
docker build -t dynamic ./dynamicWebPage
docker build -t proxy ./proxyBalancing

Run several servers and the proxy :
docker run -d --name static1 static
docker run -d --name static2 static
docker run -d --name dynamic1 dynamic
docker run -d --name dynamic2 dynamic

But you can run the proxy in interactive 
docker run -it -p 9090:8080 -p 8080:80 -v /var/run/docker.sock:/var/run/docker.sock proxy

(the -v allow to give a bigger volume to the docker container that is by default limited)

So that you can see where the request are sent concretly.
dynamic servers request should behave as round-robin and static should behave as sticky session algorithm.

you can visualize the result at demo.res.ch:8080
and you can visualize the traefik monitoring at demo.res.ch:9090

## Dynamic cluster management

### Objective

Here we want the proxy to be able to manage the creation and destruction of new servers without loss of connection.

### Manipulation

you don't knew but Traefik does it yet for you. There is nothing more to do.

### Tests

Build the images : 
docker build -t static ./staticWebPage
docker build -t dynamic ./dynamicWebPage
docker build -t proxy ./proxyBalancing

Run several servers and the proxy :
docker run -d --name static1 static
docker run -d --name static2 static
docker run -d --name dynamic1 dynamic
docker run -d --name dynamic2 dynamic
docker run -d -p 9090:8080 -p 8080:80 -v /var/run/docker.sock:/var/run/docker.sock proxy

you can visualize the result at demo.res.ch:8080
and you can visualize the traefik monitoring at demo.res.ch:9090

now you can add and delete a server
docker run -d --name dynamic3 dynamic
docker kill dynamic2

and you should see the monitoring effect at demo.res.ch:9090

