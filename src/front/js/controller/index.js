import { Controller, provide } from 'cerebral'
import { set, when, increment } from 'cerebral/operators'
import { state, props, string } from 'cerebral/tags'
import HttpProvider from '@cerebral/http'
import { httpGet, httpPost, httpPut, httpDelete} from '@cerebral/http/operators'
import {goTo} from '@cerebral/router/operators'
import StorageModule from '@cerebral/storage'
import StorageProvider from '@cerebral/storage'
import { resolveObject } from 'function-tree'

import Devtools from 'cerebral/devtools'

import router from './../modules/router'
import useragent from './../modules/useragent'

import loadFormats from './../signals/loadFormats'


const changePage = (page, continueSequence = []) => {
  return [
    set(state`currentPage`, page),
    continueSequence
  ]
}


// Initial state of our app
const controller = Controller({
    devtools: Devtools && Devtools({
        host: 'localhost:9090',
        reconnect: true
    }),
    modules: {
        router,
        useragent,
    },
    providers: [
        HttpProvider({
            baseUrl: 'http://localhost:3000/public/api/v1',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
        })
    ],
    state: {
        currentPage: 'home',
        user: {
            visitCounter: 0,
            isLoading: false,
            creditentials: {
                email: "",
                password: "",
            },
            info: null,
            error: null,
        },
        formats: {
            isLoading: false,
            list: null,
            error: null,
        },
        cart: {
            prestations: null,
        },
        orders: null,
        prestationForm: {
            format : {
                id: null,
            },
            ink: {
                id: null,
            },
            paperType: {
                id: null,
            },
            paperColor: {
                id: null,
            },
            orientation: {
                id: null,
            },

            image: {
                url: null,
                error: null,
            },
        }
    },
    signals: {
        //
        // Routes
        //
        homeRouted: changePage('home', [
            loadFormats
        ]),
        editorRouted: changePage('editor', [
            loadFormats
        ]),
        //
        // User authentification
        //
        logUserOut: [
            set(state`user.isLoading`, true),
            httpGet('/disconnect'), {
                success: [
                    set(state`user.isLoading`, false),
                    set(state`user.error`, null),
                    set(state`user.hasLoggedOut`, true),
                    set(state`user.tmpName`, state`user.info.first_name`),
                    set(state`user.info`, null),
                ],
                error: [
                    set(state`user.error`, true),
                    set(state`user.isLoading`, false),
                ]
            },
        ],
        logUserIn: [
            set(state`user.isLoading`, true),
            httpPost('/verify_account', resolveObject({
                user_email: state`user.creditentials.email`,
                user_password: state`user.creditentials.password`,
            })), {
                success: [
                    set(state`user.info`, props`response.result.current_user`),
                    set(state`user.creditentials`, {
                        email: '',
                        password: '',
                    }),
                    set(state`user.isLoading`, false),
                    set(state`user.error`, null),
                    increment(state`user.visitCounter`, 1),
                ],
                error: [
                    set(state`user.error`, true),
                    set(state`user.creditentials.password`, ''),
                    set(state`user.isLoading`, false),
                ]
            },
        ],
        inputEmailValue: [
            when(props`value`), {
                true: set(state`user.creditentials.email`, string`${props`value`}`),
                false: set(state`user.creditentials.email`, '')
            }
        ],
        inputPasswordValue: [
            when(props`value`), {
                true: set(state`user.creditentials.password`, string`${props`value`}`),
                false: set(state`user.creditentials.password`, '')
            }
        ],
        removeByeMessage: [
            set(state`user.hasLoggedOut`, null),
            set(state`user.tmpName`, null),
        ],

        //
        // User changing input
        //
        userChoseFormat: [
            set(state`newPrestationForm.format.id`, props`value`),
            when(state`currentPage`, (value) => value === 'home'), {
                true: [
                    goTo('/editor')
                ],
                false: []
            },
        ],
        uploadedPrestationFile: [
            set(state`prestationForm.image.error`, null),
            set(state`prestationForm.image.url`, props`value`),
        ],
        errorUploadingPrestationFile: [
            set(state`prestationForm.image.error`, props`value`),
            set(state`prestationForm.image.url`, null),
        ],
        //
        // Data loading
        //
        loadFormats: loadFormats,

    }
})
export default controller
