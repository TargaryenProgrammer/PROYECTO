const temaOscuro = () => {
  document.querySelector("body").setAttribute("data-bs-theme", "dark");
  document.querySelector("#dl-icon").setAttribute("class", "fa-solid fa-sun");
};

const temaClaro = () => {
  document.querySelector("body").setAttribute("data-bs-theme", "light");
  document.querySelector("#dl-icon").setAttribute("class", "fa-solid fa-moon");
};

const cambiarTema = () => {
  document.querySelector("body").getAttribute("data-bs-theme") === "light"
    ? temaOscuro()
    : temaClaro();
};
