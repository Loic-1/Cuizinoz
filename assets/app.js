import "./bootstrap.js";
import "./styles/app.css";

console.log("load assets/app.js");

const slider = document.getElementById("note-slider");

if (slider) {
  const range = noUiSlider.create(slider, {
    start: [0, 5],
    connect: true,
    step: 1,
    range: {
      min: 0,
      max: 5,
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
