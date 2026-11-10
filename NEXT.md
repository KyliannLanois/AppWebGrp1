Etape suivante :

Mettre en place la base de données dans phpmyadmin et le fichier schema.sql

faire le TouiteRepository

faire le index.php pour tester



Quentin : 
TouiteRepository et Connection






Thibaud : 

implémenter Dispatcher voir si il marche et Action.php (en abstrait)
Faire un touiteurRenderer (avec 2 modes un compact et un normal deja vu en cours)


ne pas oublier la protection contre le XSS

Personne 3 :




Etape suivante : 


faire les 3 classes object : touites, user et tag dans classes





1. afficher touites ordre chrono inverse
2. Afficher touite detail
3. 
4. Afficher touite personne
5. Afficher touite tag
6. 



Fonctionnalités finies obligatoires :

[ ] 1- Afficher liste touites ordre chrono inverse (plus récent au début) 
[ ] 2- Afficher touite détail (texte entier, le score, l'auteur etc)
[ ] 4- Afficher touites personne donnée (accessible si on clique sur le nom)
[ ] 5- Afficher touites associé a tag (si on clique sur un tag)

[ ] 6- Publier un touite partage un touite avec affiché son username
[ ] 8- Gestion des tags : lors de la publication d'un nouveau touite, on extrait les tags éventuels du touite (plus utilisé ?)

[ ] 9- Mur d'un utilisateur page d'accueil d'un user. Affiche les touites qui l'interesse le plus
touite des personnes suivies, ainsi que les tag suivis aussi affiché par ordre chronologique inversé
[ ] 10- Suivre des users a la consultation d'un touite il peu s'abonner a l'utilisateur qui l'a posté
[ ] 11- S'abonner a un tag : possibilité de s'abonner a un tag (SUR son mur d'accueil peut entrer un tag pour sy abonner !! DONC une recherche)

[ ] 14 - Influenceurs : afficher listes des plus influents : donc ceux les plus suivis liste affiché ordre croissant nbr de followers
