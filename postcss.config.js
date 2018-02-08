module.exports = {
  plugins: [
    require("postcss-easy-import"),
    require("postcss-cssnext"),
    require("tailwindcss")("./tailwind.js")
  ]
};
