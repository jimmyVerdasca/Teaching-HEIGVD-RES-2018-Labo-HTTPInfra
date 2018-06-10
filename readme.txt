#se déplacer dans le bon sous dossier
cd D:/Semestre4/RES/Labo2/Teaching-HEIGVD-RES-2018-Labo-HTTPInfra

#construire les images
docker build -t static ./staticWebPage
docker build -t dynamic ./dynamicWebPage
docker build -t proxy ./proxyBalancing
OU
docker build -t proxy ./proxy

#lancer les container
docker run -d --name static static
docker run -d --name dynamic dynamic

OU
docker run -d --name static1 static
docker run -d --name static2 static
docker run -d --name dynamic1 dynamic
docker run -d --name dynamic2 dynamic

#regarder les adresses données automatiquement par docker
docker inspect static | grep -i ipaddr
#réponse 172.17.0.2
docker inspect dynamic | grep -i ipaddr
#réponse 172.17.0.3

#lancement du container proxy
docker run -d -e STATIC_APP=172.17.0.2:80 -e DYNAMIC_APP=172.17.0.3:3000 --name proxy -p 8080:80 proxy

OU

docker run -p 8080:80 -e STATIC_APP1=172.17.0.2:80 -e STATIC_APP2=172.17.0.3:80 -e DYNAMIC_APP1=172.17.0.4:3000 -e DYNAMIC_APP2=172.17.0.5:3000 proxy

OU

docker run -d -p 9090:8080 -p 8080:80 -v /var/run/docker.sock:/var/run/docker.sock proxy
on peut observer le monitoring sur http://demo.res.ch:9090 ou le site sur http://demo.res.ch:8080


#pour repartir de 0
stop all containers:
docker kill $(docker ps -q)

remove all containers
docker rm $(docker ps -a -q)

remove all docker images
docker rmi $(docker images -q)