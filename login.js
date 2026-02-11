document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("loginFormElement");
  const msg = document.getElementById("loginMessage");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    msg.style.display = "none";
    msg.textContent = "";

    const formData = new FormData(this);

    fetch("login.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {

      msg.style.display = "block";
      msg.textContent = data.message;
      msg.className = "mt-3 text-center " + 
        (data.success ? "text-success" : "text-danger");

      if (data.success) {
        setTimeout(() => {
          window.location.href = "dashboard.html";
        }, 800); // small delay so user sees success
      }

    })
    .catch(err => {
      msg.style.display = "block";
      msg.textContent = "Something went wrong. Please try again.";
      msg.className = "mt-3 text-center text-danger";
      console.error(err);
    });
  });

});
