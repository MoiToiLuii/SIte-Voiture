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