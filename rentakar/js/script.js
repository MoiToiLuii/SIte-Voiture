/* =========================
   AVIS CLIENTS (SAFE)
========================= */

const avis = [
    { nom: "Karim B.", commentaire: "Service rapide et très professionnel. Large choix de véhicules.", note: 5 },
    { nom: "Sarah M.", commentaire: "Très bonne expérience, site simple et efficace.", note: 5 },
    { nom: "Thomas L.", commentaire: "Location facile et rapide, je recommande vivement.", note: 5 },
    { nom: "Julie R.", commentaire: "Voiture impeccable, service client au top.", note: 4 },
    { nom: "Mehdi A.", commentaire: "Prix corrects et réservation ultra simple.", note: 5 },
    { nom: "Laura P.", commentaire: "J’ai trouvé exactement la voiture qu’il me fallait.", note: 4 }
];

const zone = document.getElementById("avis-dynamique");

if (zone) {
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
}


/* =========================
   DESCRIPTIONS VOITURES
========================= */

const descriptions = {
    clio: "Renault Clio – Citadine polyvalente, idéale pour la ville et les trajets quotidiens. Très faible consommation, excellente maniabilité. Puissance : environ 90 ch.",
    "208": "Peugeot 208 – Moderne, confortable et dynamique. Très agréable à conduire, parfaite pour un usage urbain et périurbain. Puissance : environ 100 ch.",
    "i20": "Hyundai i20 – Fiable, économique et bien équipée. Très bon rapport qualité/prix, idéale pour les conducteurs recherchant simplicité et efficacité. Puissance : environ 84 ch.",
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


/* =========================
   TARIFS PAR MODÈLE
========================= */

const tarifs = {
    clio: 45,
    "208": 50,
    i20: 48,
    yaris: 47,
    polo: 52,
    focus: 55,
    classec: 90,
    classee: 110,
    serie3: 95,
    a4: 100,
    x1: 120,
    q5: 130,
    p911: 350,
    r8: 500,
    amggt: 600
};




/* =========================
   POPUP DESCRIPTION (SAFE)
========================= */

function ouvrirPopup(modele) {

    if (!descriptions[modele]) {
        console.error("Modèle inconnu :", modele);
        return;
    }

    const texte = descriptions[modele];
    const titre = texte.split("–")[0];

    const titreEl = document.getElementById("titre-descriptif");
    const texteEl = document.getElementById("texte-descriptif");
    const popup = document.getElementById("popup-descriptif");

    if (!titreEl || !texteEl || !popup) return;

    titreEl.textContent = titre;
    texteEl.textContent = texte;

    popup.style.display = "flex";
}

function fermerPopup() {
    const popup = document.getElementById("popup-descriptif");
    if (popup) popup.style.display = "none";
}


/* =========================
   POPUP LOCATION (SAFE)
========================= */

function ouvrirPopupLocation(modele) {

    const popup = document.getElementById("popup-location");
    const modeleLocation = document.getElementById("modele-location");
    const inputVoiture = document.getElementById("voiture");
    const tarifEl = document.getElementById("tarif-location");

    if (!popup || !modeleLocation || !inputVoiture || !tarifEl) return;
    if (!descriptions[modele]) return;

    popup.style.display = "flex";
    modeleLocation.textContent = descriptions[modele].split("–")[0];
    inputVoiture.value = modele;

    tarifEl.textContent = tarifs[modele] + " € / jour";

    calculerTotal();
}

function fermerPopupLocation() {
    const popup = document.getElementById("popup-location");
    if (popup) popup.style.display = "none";
}


/* =========================
   CALCUL AUTOMATIQUE DU TOTAL
========================= */

function calculerTotal() {
    const dateDebut = document.querySelector("input[name='date_debut']").value;
    const dateFin = document.querySelector("input[name='date_fin']").value;
    const modele = document.getElementById("voiture").value;
    const totalEl = document.getElementById("total-location");

    if (!dateDebut || !dateFin || !modele) return;

    const debut = new Date(dateDebut);
    const fin = new Date(dateFin);

    const diff = fin - debut;
    const jours = diff / (1000 * 60 * 60 * 24);

    if (jours <= 0) {
        totalEl.textContent = "Dates invalides";
        return;
    }

    const prixJour = tarifs[modele];
    const total = prixJour * jours;

    totalEl.textContent = total + " €";
}


// Mise à jour automatique quand les dates changent
document.addEventListener("input", function(e) {
    if (e.target.name === "date_debut" || e.target.name === "date_fin") {
        calculerTotal();
    }
});


