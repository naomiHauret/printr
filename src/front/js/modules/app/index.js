import { Module, provide, parallel } from 'cerebral'
import { set, when, increment } from 'cerebral/operators'
import { state, props, string } from 'cerebral/tags'
import HttpProvider from '@cerebral/http'
import { httpGet, httpPost, httpPut, httpDelete } from '@cerebral/http/operators'
import { goTo } from '@cerebral/router/operators'
import StorageModule from '@cerebral/storage'
import StorageProvider from '@cerebral/storage'
import { resolveObject } from 'function-tree'

// modules
import router from './../router'
import useragent from './../useragent'

// providers
import http from './../../providers/http'

// signals
import * as userAuthActions from './../../actions/userAuthentification'
import * as prestationFormActions from './../../actions/prestationForm'

const changePage = (page, continueSequence = []) => {
    return [
        set(state`currentPage`, page),
        continueSequence
    ]
}

export default Module({
  modules: {
    router,
    useragent
  },
  providers: {
    http
  },
  state: {
    currentPage: "home",
    user: {
      visitCounter: 0,
      isLoading: false,
      creditentials: {
        email: "",
        password: ""
      },
      info: null,
      error: null
    },
    formats: {
      isLoading: false,
      list: null,
      error: null
    },
    cart: {
      prestations: null
    },
    orders: null,
    prestationForm: {
      format: {
        id: null
      },
      image: {
        url: null,
        error: null
      },
      options: {
        isLoading: null,
        error: null,
        list: null,
        filled: null
      },
      quantity: null
    }
  },
  signals: {
    //
    // Routes
    //
    homeRouted: changePage("home", [prestationFormActions.loadFormats]),
    editorAddPrestationRouted: changePage("editor", [
      parallel([
        prestationFormActions.loadFormats,
        prestationFormActions.loadOptions
      ])
    ]),
    editorEditPrestationRouted: changePage("editor/:id", [
      parallel([
        prestationFormActions.loadFormats,
        prestationFormActions.loadOptions
      ])
    ]),

    //
    // User authentification
    //
    logUserOut: userAuthActions.logUserOut,
    logUserIn: userAuthActions.logUserIn,
    inputEmailValue: userAuthActions.inputEmailValue,
    inputPasswordValue: userAuthActions.inputPasswordValue,

    //
    // User changing input
    //
    userChoseFormat: prestationFormActions.userChoseFormat,
    uploadedPrestationFile: prestationFormActions.uploadedPrestationFile,
    errorUploadingPrestationFile:
      prestationFormActions.errorUploadingPrestationFile,
    toggleOption: prestationFormActions.toggleOption,
    setQuantity: prestationFormActions.setQuantity,

    //
    // Data loading
    //
    loadFormats: prestationFormActions.loadFormats
  }
});
