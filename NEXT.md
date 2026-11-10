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

[ X ] 1- Afficher liste touites ordre chrono inverse (plus récent au début) 
[ X ] 2- Afficher touite détail (texte entier, le score, l'auteur etc)
[ X ] 4- Afficher touites personne donnée (accessible si on clique sur le nom)
[ X ] 5- Afficher touites associé a tag (si on clique sur un tag)

[ X ] 6- Publier un touite partage un touite avec affiché son username
[ X ] 8- Gestion des tags : lors de la publication d'un nouveau touite, on extrait les tags éventuels du touite (plus utilisé ?)

[ X ] 9- Mur d'un utilisateur page d'accueil d'un user. Affiche les touites qui l'interesse le plus
touite des personnes suivies, ainsi que les tag suivis aussi affiché par ordre chronologique inversé
[ X ] 10- Suivre des users a la consultation d'un touite il peu s'abonner a l'utilisateur qui l'a posté
[ X ] 11- S'abonner a un tag : possibilité de s'abonner a un tag (SUR son mur d'accueil peut entrer un tag pour sy abonner !! DONC une recherche)

[ X ] 14 - Influenceurs : afficher listes des plus influents : donc ceux les plus suivis liste affiché ordre croissant nbr de followers


3 - Paginer la liste des touites : si le nombre de touites dans la liste est trop important,
permettre à la liste des touites de s’afficher par morceau. Les utilisateurs peuvent passer
d’une page à la suivante et revenir en arrière.

[ X ] 7 Évaluer un touite : un utilisateur peut incrémenter (like) ou réduire (dislike) de 1 la note
d’un touite qu’il consulte. L’utilisateur ne peut évaluer un même touite qu’une seule fois.


12L’utilisateur narcissique : un utilisateur peut afficher les noms des utilisateurs qui le
suivent, ainsi que le score moyen de ses touites.

[ pas a moi] 13 Associer une image à un touite : On peut ajouter une image lorsqu’on rédige un
nouveau touite. L’auteur du touite peut dans ce cas choisir un fichier image qui sera
transféré au serveur. L’image sera affichée avec le touite quand celui-ci est affiché en
détail (pas dans une liste)


15 Les tendances : afficher la liste des tags les plus mentionnés, avec le nombre de fois
où ils sont mentionnés, la liste est ordonnée par ordre croissant.

16 Le back-office : une application indépendante de touiteur.app qui permet aux
    administrateurs de réaliser les deux fonctionnalités précédentes.