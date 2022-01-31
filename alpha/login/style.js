function switchTo (type) {
	switch (type) {
		case "SignIn":
			// Change header and paragraph content
			document.getElementById("formHeader").innerHTML = "Accedi";
			document.getElementById("formParagraph").innerHTML = "Inserisci le tue credenziali";

			// Remove inputBox <div> for passwordCheck <input>
			document.getElementById("form").removeChild(document.getElementById("passwordCheckBox"));

			// Change submitContainer content			
			document.getElementById("switchLabel").setAttribute("onclick", "switchTo('SignUp')");
			document.getElementById("switchLabel").innerHTML = "Crea un account";
			document.getElementById("submitInput").name = "sign-in";
			document.getElementById("submitInput").value = "Accedi";
			document.getElementById("submitInput").setAttribute("onclick", "SignIn_SendData(document.getElementById('usernameInput').value, document.getElementById('passwordInput').value);");
		break;
		case "SignUp":
			// Change header and paragraph content
			document.getElementById("formHeader").innerHTML = "Registrati";
			document.getElementById("formParagraph").innerHTML = "Crea il tuo account";
			
			// Add inputBox <div> for passwordCheck <input>
			let newInput = document.createElement("input");
			newInput.setAttribute("id", "passwordCheckInput");
			newInput.setAttribute("type", "password");
			newInput.setAttribute("name", "passwordCheck");
			newInput.setAttribute("required", "");
			newInput.setAttribute("onkeyup", "this.setAttribute('value', this.value);");
			newInput.setAttribute("value", "");
			let newLabel = document.createElement("label");
			newLabel.setAttribute("id", "passwordCheckLabel");
			newLabel.innerHTML = "Conferma Password";
			let newInputBox = document.createElement("div");
			newInputBox.setAttribute("class", "inputBox");
			newInputBox.setAttribute("id", "passwordCheckBox");
			newInputBox.appendChild(newInput);
			newInputBox.appendChild(newLabel);
			document.getElementById("form").insertBefore(newInputBox, document.getElementById("submitContainer"));

			// Change submitContainer content	
			document.getElementById("switchLabel").setAttribute("onclick", "switchTo('SignIn')");
			document.getElementById("switchLabel").innerHTML = "Accedi";
			document.getElementById("submitInput").name = "sign-up";
			document.getElementById("submitInput").value = "Registrati";
			document.getElementById("submitInput").setAttribute("onclick", "SignUp_SendData(document.getElementById('usernameInput').value, document.getElementById('passwordInput').value, document.getElementById('passwordCheckInput').value);");
		break;
		default:
		break;
	}
}