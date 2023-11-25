document.addEventListener("DOMContentLoaded", function () {
    var usernameInput = document.getElementById("username");
    var emailInput = document.getElementById("email-confirm");
    var passwordInput = document.getElementById("password");
    var errorMessage = document.getElementById("error-message");
    var emailValidationMessage = document.getElementById("email-validation");

    // Vérification en temps réel du nom d'utilisateur
    usernameInput.addEventListener("input", function () {
        var username = usernameInput.value;

        // Vérifier si la longueur du nom d'utilisateur est supérieure à 0
        if (username.length > 0) {
            // Effectuer une requête AJAX pour vérifier la disponibilité du nom d'utilisateur
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../scripts/check_username.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        var response = xhr.responseText;

                        if (response === "exists") {
                            setError(errorMessage, "Ce nom d'utilisateur existe déjà.");
                        } else {
                            clearError(errorMessage);
                        }
                    }
                }
            };

            // Envoyer la requête avec les données du formulaire
            xhr.send("username=" + encodeURIComponent(username));
        } else {
            // Afficher une erreur si la longueur du nom d'utilisateur est inférieure à 1
            setError(errorMessage, "Veuillez saisir au moins 1 caractère.");
        }
    });

    // Vérification en temps réel de l'adresse e-mail
    emailInput.addEventListener("input", function () {
        var email = emailInput.value;
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (emailRegex.test(email)) {
            clearError(emailValidationMessage);
        } else {
            setError(emailValidationMessage, "Veuillez saisir une adresse e-mail valide.");
        }
    });

    // Soumission du formulaire d'inscription
    document.getElementById("submit-button").addEventListener("click", function () {
        var username = usernameInput.value;
        var email = emailInput.value;
        var password = passwordInput.value;

        // Effectuer d'autres vérifications si nécessaire

        // Envoyer les données du formulaire à votre script PHP pour le traitement
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../scripts/register_user.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var response = xhr.responseText;

                    if (response === "success") {
                        // Afficher un message de confirmation
                        showConfirmationMessage("Votre inscription a bien été prise en compte. Un e-mail vous a été envoyé.");

                        // Rediriger l'utilisateur vers une page de confirmation
                        // window.location.href = "confirmation.php";
                    } else {
                        // Afficher un message d'erreur
                        setError(errorMessage, "Erreur lors de l'inscription.");
                    }
                } else {
                    // Afficher un message d'erreur en cas d'échec de la requête
                    setError(errorMessage, "Erreur lors de la communication avec le serveur.");
                }
            }
        };

        // Envoyer la requête avec les données du formulaire
        xhr.send("username=" + encodeURIComponent(username) + "&email=" + encodeURIComponent(email) + "&password=" + encodeURIComponent(password));
    });

    // Fonction pour définir un message d'erreur
    function setError(element, message) {
        element.textContent = message;
        element.classList.add("error-message");
    }

    // Fonction pour effacer un message d'erreur
    function clearError(element) {
        element.textContent = "";
        element.classList.remove("error-message");
    }

    // Fonction pour afficher un message de confirmation
    function showConfirmationMessage(message) {
        var confirmationMessage = document.createElement("div");
        confirmationMessage.textContent = message;
        confirmationMessage.classList.add("confirmation-message");
        document.body.appendChild(confirmationMessage);

        // Supprimer le message de confirmation après quelques secondes
        setTimeout(function () {
            document.body.removeChild(confirmationMessage);
        }, 5000); // 5000 millisecondes (5 secondes)
    }
});
