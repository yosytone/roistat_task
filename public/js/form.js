setTimeout(() => {
    document.getElementById("over_30_sec").value = "1";
  }, 30000);
  
  document.querySelector("form").addEventListener("submit", function (event) {
    event.preventDefault();
  
    const formData = new FormData(this);
    const submitButton = this.querySelector("button[type='submit']");
  
    submitButton.disabled = true;
    submitButton.classList.add("form__button--loading");
    submitButton.textContent = "Загрузка, пожалуйста подождите...";
  
    fetch("/submit.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        submitButton.disabled = false;
        submitButton.classList.remove("form__button--loading");
        submitButton.textContent = "Отправить заявку";
  
        if (data.status === "success") {
          alert(data.message);
          this.reset();
        } else {
          alert(data.message);
        }
      })
      .catch((error) => {
        submitButton.disabled = false;
        submitButton.classList.remove("form__button--loading");
        submitButton.textContent = "Отправить заявку";
  
        console.error("Ошибка при отправке формы:", error);
      });
  });
  