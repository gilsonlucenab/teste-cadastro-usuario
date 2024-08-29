document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registration-form");
  const passwordInput = document.getElementById("password");

  passwordInput.addEventListener("input", (event) => {
      event.target.value = event.target.value.replace(/[^0-9]/g, "");
  });

  form.addEventListener("submit", (event) => {
      event.preventDefault();
      clearErrors();

      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim();
      const password = passwordInput.value;

      let isValid = true;
      if (!name) {
          showError("name-error", "Campo obrigatório.");
          isValid = false;
      }
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          showError("email-error", "Email inválido.");
          isValid = false;
      }
      if (password.length < 8) {
          showError("password-error", "Senha deve conter pelo menos 8 números.");
          isValid = false;
      }

      if (isValid) {
          fetch("php/config.php", {
              method: "POST",
              body: new FormData(form),
          })
          .then(response => response.json())
          .then(data => {
              alert(data.message);
              if (data.success) form.reset();
          })
          .catch(error => {
              console.error("Erro ao enviar os dados:", error);
              alert("Ocorreu um erro ao enviar os dados.");
          });
      }
  });

  const showError = (id, msg) => {
      const el = document.getElementById(id);
      if (el) el.textContent = msg;
  };

  const clearErrors = () => {
      document.querySelectorAll(".error-message").forEach(el => el.textContent = "");
  };
});
