#se déplacer dans le bon sous dossier
cd D:/Semestre4/RES/Labo2/Teaching-HEIGVD-RES-2018-Labo-HTTPInfra

#construire les images
docker build -t static ./staticWebPage
docker build -t dynamic ./dynamicWebPage
docker build -t proxy ./proxyBalancing

#lancer les container
docker run -d --name static static
docker run -d --name dynamic dynamic

#regarder les adresses données automatiquement par docker
docker inspect static | grep -i ipaddr
#réponse 172.17.0.2
docker inspect dynamic | grep -i ipaddr
#réponse 172.17.0.3

#lancement du container proxy
docker run -d -e STATIC_APP=172.17.0.2:80 -e DYNAMIC_APP=172.17.0.3:3000 --name proxy -p 8080:80 proxy

#pour repartir de 0
stop all containers:
docker kill $(docker ps -q)

remove all containers
docker rm $(docker ps -a -q)

remove all docker images
docker rmi $(docker images -q)