import "babel-polyfill"
import Inferno from "inferno"

const app = document.querySelector("#app")
app !== null && Inferno.render(<h1>Inferno set up & ready to unleash HELLFIRE</h1>, app)