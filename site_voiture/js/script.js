/* -------------------------
   AVIS CLIENTS (déjà OK)
-------------------------- */

const avis = [
    { nom: "Karim B.", commentaire: "Service rapide et très professionnel. Large choix de véhicules.", note: 5 },
    { nom: "Sarah M.", commentaire: "Très bonne expérience, site simple et efficace.", note: 5 },
    { nom: "Thomas L.", commentaire: "Location facile et rapide, je recommande vivement.", note: 5 },
    { nom: "Julie R.", commentaire: "Voiture impeccable, service client au top.", note: 4 },
    { nom: "Mehdi A.", commentaire: "Prix corrects et réservation ultra simple.", note: 5 },
    { nom: "Laura P.", commentaire: "J’ai trouvé exactement la voiture qu’il me fallait.", note: 4 }
];

const zone = document.getElementById("avis-dynamique");
let index = 0;

function afficherAvis() {
    const client = avis[index];

    zone.innerHTML = `
        <div class="carte-avis animate">
            <div class="etoiles">${"★".repeat(client.note)}${"☆".repeat(5 - client.note)}</div>
            <p><strong>${client.nom}</strong></p>
            <p>${client.commentaire}</p>
        </div>
    `;

    index = (index + 1) % avis.length;
}

afficherAvis();
setInterval(afficherAvis, 4000);


/* -------------------------
   DESCRIPTIONS DES VOITURES
-------------------------- */

const descriptions = {
    clio: "Renault Clio – Citadine polyvalente, idéale pour la ville et les trajets quotidiens. Très faible consommation, excellente maniabilité. Puissance : environ 90 ch.",
    208: "Peugeot 208 – Moderne, confortable et dynamique. Très agréable à conduire, parfaite pour un usage urbain et périurbain. Puissance : environ 100 ch.",
    i20: "Hyundai i20 – Fiable, économique et bien équipée. Très bon rapport qualité/prix, idéale pour les conducteurs recherchant simplicité et efficacité. Puissance : environ 84 ch.",
    yaris: "Toyota Yaris GR – Version sportive dérivée du rallye, dotée d’un moteur 1.6 turbo de 261 ch, transmission intégrale et comportement ultra dynamique.",
    polo: "Volkswagen Polo – Finition haut de gamme, confort allemand et tenue de route exemplaire. Très stable sur autoroute. Puissance : environ 95 ch.",
    focus: "Ford Focus – Compacte polyvalente, idéale pour la ville comme pour les longs trajets. Très bonne tenue de route et moteur réactif. Puissance : environ 120 ch.",
    classec: "Mercedes-Benz Classe C – Berline premium alliant confort, élégance et technologies modernes. Très agréable sur route, excellente insonorisation. Puissance : environ 170 ch.",
    classee: "Mercedes-Benz Classe E – Grande berline luxueuse, idéale pour les longs trajets. Confort exceptionnel, technologies avancées. Puissance : environ 200 ch.",
    serie3: "BMW Série 3 – Berline sportive réputée pour son dynamisme et son plaisir de conduite. Direction précise, châssis équilibré. Puissance : environ 184 ch.",
    a4: "Audi A4 – Berline premium élégante et technologique. Confort remarquable, finition impeccable. Puissance : environ 150 ch.",
    x1: "BMW X1 – SUV compact dynamique et polyvalent. Position de conduite haute, habitacle spacieux. Puissance : environ 150 ch.",
    q5: "Audi Q5 – SUV premium confortable et raffiné. Excellente insonorisation, technologies avancées. Puissance : environ 190 ch.",
    p911: "Porsche 911 – Icône sportive par excellence. Moteur flat-six, comportement ultra précis, accélérations impressionnantes. Puissance : environ 385 ch.",
    r8: "Audi R8 – Supercar équipée d’un moteur V10 atmosphérique. Sonorité incroyable, performances explosives. Puissance : environ 570 ch.",
    amggt: "Mercedes-AMG GT – Coupé sportif hautes performances. Moteur V8 biturbo, conduite très dynamique. Puissance : environ 530 ch."
};


/* -------------------------
   POPUP DESCRIPTIF
-------------------------- */

function ouvrirPopup(modele) {
    const contenu = document.getElementById("contenu-popup");
    contenu.innerHTML = `<p>${descriptions[modele]}</p>`;
    document.getElementById("popup").style.display = "flex";
}

function fermerPopup() {
    document.getElementById("popup").style.display = "none";
}
