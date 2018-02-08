import 'babel-polyfill'
import Inferno from 'inferno'
import "./css/main.css"

// Router related
import Routes from './js/routes'

// State management related
import { Container } from '@cerebral/inferno'
import controller from "./js/controller"


// Bootstrapping app
const app = document.querySelector("#app")
app !== null && Inferno.render(
    <Container controller={controller}>
        <Routes />
    </Container>,
    app
)

