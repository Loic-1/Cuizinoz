"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["app"],{

/***/ "./assets/app.js":
/*!***********************!*\
  !*** ./assets/app.js ***!
  \***********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _bootstrap_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./bootstrap.js */ "./assets/bootstrap.js");
/* harmony import */ var _styles_app_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./styles/app.css */ "./assets/styles/app.css");


console.log("load assets/app.js");
var slider = document.getElementById("note-slider");
if (slider) {
  var range = noUiSlider.create(slider, {
    start: [20, 80],
    connect: true,
    range: {
      min: 0,
      max: 100
    }
  });
  var noteMin = document.getElementById("noteMin");
  var noteMax = document.getElementById("noteMax");
  range.on("slide", function (values, handle) {
    if (handle === 0) {
      noteMin.value = Math.round(values[0]);
    }
    if (handle === 1) {
      noteMax.value = Math.round(values[1]);
    }
    console.log(values, handle);
  });
}

/***/ }),

/***/ "./assets/bootstrap.js":
/*!*****************************!*\
  !*** ./assets/bootstrap.js ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _symfony_stimulus_bundle__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @symfony/stimulus-bundle */ "./node_modules/@symfony/stimulus-bundle/dist/loader.js");

var app = (0,_symfony_stimulus_bundle__WEBPACK_IMPORTED_MODULE_0__.startStimulusApp)();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

/***/ }),

/***/ "./assets/styles/app.css":
/*!*******************************!*\
  !*** ./assets/styles/app.css ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_symfony_stimulus-bundle_dist_loader_js"], () => (__webpack_exec__("./assets/app.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwLmpzIiwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7OztBQUF3QjtBQUNFO0FBRTFCQSxPQUFPLENBQUNDLEdBQUcsQ0FBQyxvQkFBb0IsQ0FBQztBQUVqQyxJQUFNQyxNQUFNLEdBQUdDLFFBQVEsQ0FBQ0MsY0FBYyxDQUFDLGFBQWEsQ0FBQztBQUVyRCxJQUFJRixNQUFNLEVBQUU7RUFDVixJQUFNRyxLQUFLLEdBQUdDLFVBQVUsQ0FBQ0MsTUFBTSxDQUFDTCxNQUFNLEVBQUU7SUFDdENNLEtBQUssRUFBRSxDQUFDLEVBQUUsRUFBRSxFQUFFLENBQUM7SUFDZkMsT0FBTyxFQUFFLElBQUk7SUFDYkosS0FBSyxFQUFFO01BQ0xLLEdBQUcsRUFBRSxDQUFDO01BQ05DLEdBQUcsRUFBRTtJQUNQO0VBQ0YsQ0FBQyxDQUFDO0VBRUYsSUFBTUMsT0FBTyxHQUFHVCxRQUFRLENBQUNDLGNBQWMsQ0FBQyxTQUFTLENBQUM7RUFDbEQsSUFBTVMsT0FBTyxHQUFHVixRQUFRLENBQUNDLGNBQWMsQ0FBQyxTQUFTLENBQUM7RUFFbERDLEtBQUssQ0FBQ1MsRUFBRSxDQUFDLE9BQU8sRUFBRSxVQUFVQyxNQUFNLEVBQUVDLE1BQU0sRUFBRTtJQUMxQyxJQUFJQSxNQUFNLEtBQUssQ0FBQyxFQUFFO01BQ2RKLE9BQU8sQ0FBQ0ssS0FBSyxHQUFHQyxJQUFJLENBQUNDLEtBQUssQ0FBQ0osTUFBTSxDQUFDLENBQUMsQ0FBQyxDQUFDO0lBQ3pDO0lBRUEsSUFBSUMsTUFBTSxLQUFLLENBQUMsRUFBRTtNQUNkSCxPQUFPLENBQUNJLEtBQUssR0FBR0MsSUFBSSxDQUFDQyxLQUFLLENBQUNKLE1BQU0sQ0FBQyxDQUFDLENBQUMsQ0FBQztJQUN6QztJQUVBZixPQUFPLENBQUNDLEdBQUcsQ0FBQ2MsTUFBTSxFQUFFQyxNQUFNLENBQUM7RUFDN0IsQ0FBQyxDQUFDO0FBQ0o7Ozs7Ozs7Ozs7OztBQy9CNEQ7QUFFNUQsSUFBTUssR0FBRyxHQUFHRCwwRUFBZ0IsQ0FBQyxDQUFDO0FBQzlCO0FBQ0E7Ozs7Ozs7Ozs7O0FDSkEiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvYXBwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9ib290c3RyYXAuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3N0eWxlcy9hcHAuY3NzIl0sInNvdXJjZXNDb250ZW50IjpbImltcG9ydCBcIi4vYm9vdHN0cmFwLmpzXCI7XHJcbmltcG9ydCBcIi4vc3R5bGVzL2FwcC5jc3NcIjtcclxuXHJcbmNvbnNvbGUubG9nKFwibG9hZCBhc3NldHMvYXBwLmpzXCIpO1xyXG5cclxuY29uc3Qgc2xpZGVyID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJub3RlLXNsaWRlclwiKTtcclxuXHJcbmlmIChzbGlkZXIpIHtcclxuICBjb25zdCByYW5nZSA9IG5vVWlTbGlkZXIuY3JlYXRlKHNsaWRlciwge1xyXG4gICAgc3RhcnQ6IFsyMCwgODBdLFxyXG4gICAgY29ubmVjdDogdHJ1ZSxcclxuICAgIHJhbmdlOiB7XHJcbiAgICAgIG1pbjogMCxcclxuICAgICAgbWF4OiAxMDAsXHJcbiAgICB9LFxyXG4gIH0pO1xyXG5cclxuICBjb25zdCBub3RlTWluID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJub3RlTWluXCIpO1xyXG4gIGNvbnN0IG5vdGVNYXggPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChcIm5vdGVNYXhcIik7XHJcblxyXG4gIHJhbmdlLm9uKFwic2xpZGVcIiwgZnVuY3Rpb24gKHZhbHVlcywgaGFuZGxlKSB7XHJcbiAgICBpZiAoaGFuZGxlID09PSAwKSB7XHJcbiAgICAgICAgbm90ZU1pbi52YWx1ZSA9IE1hdGgucm91bmQodmFsdWVzWzBdKVxyXG4gICAgfVxyXG5cclxuICAgIGlmIChoYW5kbGUgPT09IDEpIHtcclxuICAgICAgICBub3RlTWF4LnZhbHVlID0gTWF0aC5yb3VuZCh2YWx1ZXNbMV0pXHJcbiAgICB9XHJcblxyXG4gICAgY29uc29sZS5sb2codmFsdWVzLCBoYW5kbGUpO1xyXG4gIH0pO1xyXG59XHJcbiIsImltcG9ydCB7IHN0YXJ0U3RpbXVsdXNBcHAgfSBmcm9tICdAc3ltZm9ueS9zdGltdWx1cy1idW5kbGUnO1xyXG5cclxuY29uc3QgYXBwID0gc3RhcnRTdGltdWx1c0FwcCgpO1xyXG4vLyByZWdpc3RlciBhbnkgY3VzdG9tLCAzcmQgcGFydHkgY29udHJvbGxlcnMgaGVyZVxyXG4vLyBhcHAucmVnaXN0ZXIoJ3NvbWVfY29udHJvbGxlcl9uYW1lJywgU29tZUltcG9ydGVkQ29udHJvbGxlcik7XHJcbiIsIi8vIGV4dHJhY3RlZCBieSBtaW5pLWNzcy1leHRyYWN0LXBsdWdpblxuZXhwb3J0IHt9OyJdLCJuYW1lcyI6WyJjb25zb2xlIiwibG9nIiwic2xpZGVyIiwiZG9jdW1lbnQiLCJnZXRFbGVtZW50QnlJZCIsInJhbmdlIiwibm9VaVNsaWRlciIsImNyZWF0ZSIsInN0YXJ0IiwiY29ubmVjdCIsIm1pbiIsIm1heCIsIm5vdGVNaW4iLCJub3RlTWF4Iiwib24iLCJ2YWx1ZXMiLCJoYW5kbGUiLCJ2YWx1ZSIsIk1hdGgiLCJyb3VuZCIsInN0YXJ0U3RpbXVsdXNBcHAiLCJhcHAiXSwic291cmNlUm9vdCI6IiJ9