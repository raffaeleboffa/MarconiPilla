const contattiDiv = document.getElementById('contatti');

var nContatti = 0;

function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function interval() {
    getMessagesUsers();
    setInterval(getMessagesUsers, 3000);
}
interval();

async function getMessagesUsers() {
    try {        
        const response = await fetch('api/get_messagesUsers.php');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();

        const utenti = data.utenti;
        const messaggi = data.messaggi;

        var n = utenti.length;

        if (n != nContatti) {
            nContatti = n;
        } else {
            return;
        }

        var html = '';

        utenti.forEach(element => {
            html += `
                <div class="contatto">
                    <img class="profilo" src="storage/img/profili/${element.profilo}" onclick="openModal('modal_${element.id}')">
                    <div class="text_contatto">
                        <p class="nome">${element.nome} ${element.cognome}</p>
                        <p class="username">${element.username}</p>
                    </div>

                    <div class="modal" id="modal_${element.id}">
                        <div class="modal_content">
                            <img class="profilo" src="storage/img/profili/${element.profilo}">
                            <p class="nome">${element.nome} ${element.cognome}</p>
                            <p class="username">${element.username}</p>
                            <p class="bio">${element.bio}</p>
                            <button onclick="closeModal('modal_${element.id}')">Chiudi</button>
                        </div>
                    </div>
                </div>
            `;
        });

        contattiDiv.innerHTML = html;
    } catch {
        contattiDiv.innerHTML = 'Errore nel caricamento. Riprova più tardi.';
    }
}

