<!-- form.php -->
<form class="form" method="POST" action="/submit.php">
    <div class="form__field">
        <input class="form__input" type="text" name="name" placeholder="Имя" required>
    </div>
    <div class="form__field">
        <input class="form__input" type="email" name="email" placeholder="Email" required>
    </div>
    <div class="form__field">
        <input class="form__input" type="tel" name="phone" placeholder="Телефон" required>
    </div>
    <div class="form__field">
        <input class="form__input" type="number" name="price" placeholder="Цена" required>
    </div>
    <input type="hidden" name="over_30_sec" id="over_30_sec" value="0">
    <button class="form__button" type="submit">Отправить заявку</button>
</form>