let users = {};
let react_users;

let user_selected = {"UserNom" : "Veuillez sélectionner", "UserImage" : "public/elements/profile_pics/default.jpg"};
let react_user_selected;

function getUsers(){
    var xhhtp = new XMLHttpRequest();
    xhhtp.onreadystatechange = function () {
        if(this.readyState == 4 && this.status == 200){
            let response = this.responseText;
            // JSON.parse(response).forEach(element => react_users.push(element));
            JSON.parse(response).forEach(element => react_users[element.UserId] = element);
        }
    };
    xhhtp.open("GET", "rest.php?what=users", true);
    xhhtp.send();
}
function getMessages(user_id){
    var xhhtp = new XMLHttpRequest();
    xhhtp.onreadystatechange = function () {
        if(this.readyState == 4 && this.status == 200){
            let response = this.responseText;
            JSON.parse(response).forEach(element => react_message.push(element));

            let conversation = document.getElementById('messages-container');
            // console.log(conversation.scrollTop, conversation.scrollHeight)
            conversation.scrollTop = conversation.scrollHeight;
        }
    };
    xhhtp.open("GET", "rest.php?what=messages&who="+user_id, true);
    xhhtp.send();
}
//
// function sendMessage(msg) {
//     let data = {"user_id" : react_user_selected.UserId, "message" : msg};
//     // console.log(data)
//     let xhhtp = new XMLHttpRequest();
//     xhhtp.onreadystatechange = function () {
//         if (this.readyState == 4 && this.status == 200) {
//             callback(JSON.parse(this.responseText).success);
//         }
//     };
//     xhhtp.open("POST", "rest.php", true);
//     xhhtp.setRequestHeader('Content-Type', 'application/json');
//     xhhtp.send(JSON.stringify({"what": "addMessage", "data": data}));
// }

function sendMessage(msg, callback) {
    let data = {"user_id" : react_user_selected.UserId, "message" : msg.value};
    // console.log(data)
    let xhhtp = new XMLHttpRequest();
    xhhtp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            callback(JSON.parse(this.responseText));
        }
    };
    xhhtp.open("POST", "rest.php", true); // utilise une requête asynchrone
    xhhtp.setRequestHeader('Content-Type', 'application/json');
    xhhtp.send(JSON.stringify({"what": "addMessage", "data": data}));
}

let selector_contenu = `
<h3>Conversations</h3>
    <div x-data="{donnees: users}" class="user-selector">
        <template x-for="user in donnees">
            <button @click="set_user(user);" :class="{ 'div-not-read': user.NotReadCount > 0 }" class="user">
                <div >
                    <img :src="user.UserImage">
                    <div style="display: flex;flex-direction: column;align-items: flex-start;">
                        <h3 x-text="user.UserPrenom + ' ' + user.UserNom "  > </h3>
                        <span x-text="user.LastMessage"  > </span>
                        <span x-text="user.LastMessageDate" class="last-msg-date" > </span>
                    </div>
                    
                </div>
                
                <span x-show="user.NotReadCount > 0" x-text="user.NotReadCount" class="not-read"> </span>
            </button>
        </template>
    </div> 
`;
document.getElementById("main-selector").innerHTML = selector_contenu ;




let message = new Array();
let react_message;
// message.push("coucou");
// message.push("hello");
let contenu = `        
        <div x-data="{donnees: message, preview:'', user_selected: user_selected}" class="messages">
            
            <div class="user-selected">
                <img :src="user_selected.UserImage">
                <h3 x-text="user_selected.UserNom"></h3>
            </div>
            <div id="messages-container">
                <template x-for="msg in donnees">
                    <div x-bind:class="{
                        'my-message': msg.MessageExpediteur == logged_user,
                        'message': msg.MessageExpediteur != logged_user
                    }">
                    <span x-text="msg.MessageContent" class="message-content"></span>
                    <span x-text="msg.MessageDate" class="message-date"></span>
    <!--                    <span x-text="msg.MessageExpediteur"></span>-->
    <!--    <span x-text="logged_user"></span>-->
                    </div>
                </template>
                <div x-text="preview" class="message not-sent"></div>
                
            </div> 
            <div class="send-zone" x-show="user_selected.UserId">
                    <textarea x-ref="new_msg" x-model="preview"></textarea>
                    <button @click="push_msg($refs.new_msg);preview='';">
                        <img src="public/elements/icons/send.png" class="invert" alt="envoyer">
                    </button>
                </div>
        </div>
`;
document.getElementById("main").innerHTML = contenu ;

function init() {
    react_message = Alpine.reactive(message);
    getUsers();
    react_users = Alpine.reactive(users);
    react_user_selected = Alpine.reactive(user_selected);
    // react_user_selected = Alpine.reactive(user_selected);
}

window.addEventListener("load", init, false);
// document.querySelector(".send-zone button").addEventListener("click", console.log())

function push_msg(msg){
    if(msg.value.length > 0){
        sendMessage(msg, function(success) {
            if (!success.success) {
                success = {"success" : false, "msg" : false};
            }
            updateUI(success, success.msg);
            msg.value = '';
        });
    }
}

function updateUI(success, msg) {
    if (success.success) {
        react_message.push(msg);
        msg.value = '';
    }else{
        alert('Erreur d\'envoi');
    }
}
function set_user(user){
    react_message.length = 0; // supprimer les messages
    getMessages(user.UserId);
    react_users[user.UserId].NotReadCount = 0;
    Object.assign(react_user_selected, user);
}

