import "./bootstrap.js";
import "./styles/app.css";

console.log("load assets/app.js");

const slider = document.getElementById("note-slider");

if (slider) {
  const range = noUiSlider.create(slider, {
    start: [20, 80],
    connect: true,
    range: {
      min: 0,
      max: 100,
    },
  });

  const noteMin = document.getElementById("noteMin");
  const noteMax = document.getElementById("noteMax");

  range.on("slide", function (values, handle) {
    if (handle === 0) {
        noteMin.value = Math.round(values[0])
    }

    if (handle === 1) {
        noteMax.value = Math.round(values[1])
    }

    console.log(values, handle);
  });
}
