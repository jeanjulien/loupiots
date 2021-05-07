# loupiots

"Les Loupiots" est une garderie peri-scolaire associative. 

Ce software permet la creation du site permettant aux parents d'inscrire leurs enfants aux differents crenaux de la journee.  
Le cout est mis a jour en direct et les parents peuvent enregistrer leurs paiments.  
L'administrateur peut suivre l'evolution des inscription, valider la reception des paiment et gerer le caladrier.  
Eventuelement, l'administrateur a egalement la possibilite de rajouter des crenaux pour les retards ou les imprevus.

L'animateur a �galement des droits �tendus pour les paiements et les inscriptions. Il peut surtout imprimer les feuilles d'appel permettant de g�rer les enfants � la sortie de l'�cole.


## Paiement

Le paiement apparait sur chaque page du calendrier. Dans la section R�glement de la page.  
Il fait face � la section Facture du mois pr�c�dent, donc logiquement, les paiement sont par d�faut affect� au mois pr�c�dent.  
Le calcul de la facture est donc d�cal�e d'un mois. C'est � dire que sur la page du mois *Novembre*, apparait la facture pour le mois *d'Octobre*.

S'il n'y a pas de paiement tous les mois, les r�servations sont report�es sur le mois suivant dans la rubrique du *restant du*.

### Cycle de vie d'un paiement
1. **En attente de r�ception** Apr�s c�ration, statut par d�faut.
2. **Recu** En cas de paiement par ch�que ou espece, l'animateur (ou l'administrateur) qui recoit physiquement le paiement change acquite la reception.
3. **Valid�** Le comptable (administrateur) valide le paiement (paiement physique conforme � la d�claration).
4. **Annul�** Le comptable (administrateur) annule le paiement si il n'y a pas eu de paiement physique corespondant.
5. **Comptabilis�** Le 6 de chaque mois, le syst�me comptabilise automatiquement les paiements valid�s. Ils servent donc au calcul du restant du.

### Calcul du montant du
Le montant du pour le mois ets la somme du  
- Restant du du mois pr�cedent  
- La somme de toutes les r�servations du mois factur� (standard + d�placement)  
C'est pour tenir compte des d�pacements que les factures sont �dit�es sur le mois pr�c�dent et non sur le mois courant.

### Calcul du restant du
Le restant du du mois pr�c�dent est calcul� le 6 de chaque mois.
Il prend en compte tous les paiements **valid�s** au cours du mois quelque soit la date de cr�ation du paiement ou le mois pay�.  
Ce restant du sert alors de base pour �tablir la facture du mois suivant.  

### Ajout/modification de paiement
- **Par l'utilisateur:**  
	Dans la page de r�servation du mois en cours, clicker sur *ajouter paiement*
	Le paiement est alors par d�faut dat� du mois pr�cedent ie celui corespondant � la facture affich� dans la page.
	L'utilisateur a la possibilit� de dater la facture au mois courant.
- **Par L'animateur:**  
	L'animateur doit cocher que le paiement est recu.
	- A partir de la page d'un mois pass� ou il y a deja un paiement, clicker sur *modifier* ou *ajouter paiement*
	- A partir de  la page Administration -> Facturation, choisir le bon mois, puis la famille et clicker sur *modifier* ou *ajouter paiement*
	L'animateur peut aussi cr�er un nouveau paiement
- **Par l'administrateur:**  
	L'administrateur doit valider un paiement.	
	Les acces sont similaires � ceux de l'animateur.  
La modification d'un paiement permet de changer le montant, le type de paiement et le statut.

	




	