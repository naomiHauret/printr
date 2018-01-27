import { Controller } from 'cerebral'
import Devtools from 'cerebral/devtools'

import app from './../modules/app'

let devtools = null
const IS_DEVELOPING = (process.env.NODE_ENV === "development" || process.env.NODE_ENV === "dev")
if (IS_DEVELOPING) {
    devtools = require('cerebral/devtools').default({
        host: 'localhost:9009',
        reconnect: true
    })
}

const controller = Controller(app, {
    devtools,
})

export default controller