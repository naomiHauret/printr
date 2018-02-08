import { set, when } from 'cerebral/operators'
import { state, props } from 'cerebral/tags'
import { goTo } from '@cerebral/router/operators'

//
// Data loading
//
export function loadFormats({ http, state }) {
    state.set(`formats.isLoading`, true)
    return http.get('/formats')
        .then(response => {
            state.set(`formats.isLoading`, false)
            state.set(`formats.list`, response.result.formats)
            state.set(`formats.error`, null)
        })
        .catch(error => {
            state.set(`formats.error`, true)
            state.set(`formats.isLoading`, false)
        })
}

export function loadOptions({ http, state }) {
    state.set(`prestationForm.options.isLoading`, true)
    return http.get('/options')
        .then((response) => {
            let options = response.result.options
            options.map(option => {
                option.choices.map(choice => {
                    choice.checked = false
                })
            })
            state.set(`prestationForm.options.isLoading`, false)
            state.set(`prestationForm.options.list`, options)
            state.set(`prestationForm.options.error`, null)
        })
        .catch((error) => {
            state.set(`prestationForm.options.error`, true)
            state.set(`prestationForm.options.isLoading`, false)
        })
}

//
// User input on form
//
export const userChoseFormat = [
    set(state`prestationForm.format.id`, props`value`),
    when(state`currentPage`, (value) => value === 'home'), {
        true: [
            goTo('/editor')
        ],
        false: []
    },
]

export const uploadedPrestationFile = [
    set(state`prestationForm.image.error`, null),
    set(state`prestationForm.image.url`, props`value`),
]

export const errorUploadingPrestationFile = [
    set(state`prestationForm.image.error`, props`value`),
    set(state`prestationForm.image.url`, null),
]

export function toggleOption({ state, props }) {
    let chosenOptions = []
    state.get('prestationForm.options.list').map((option, indexOptions) => {
        if(option.category == props.category) {
            option.choices.map((choice, indexChoices) => {
                choice._id != props.id
                ?   state.set(`prestationForm.options.list.${indexOptions}.choices.${indexChoices}.checked`, false)
                :   state.set(`prestationForm.options.list.${indexOptions}.choices.${indexChoices}.checked`, true)

                state.set(`prestationForm.options.list.${indexOptions}.chosen`, true)
                state.set(`prestationForm.options.list.${indexOptions}.changed`, true)
            })
        }

        option.chosen === true && chosenOptions.push(option.category)
    })

    chosenOptions.length === state.get("prestationForm.options.list").length && state.set("prestationForm.options.filled", true)
}

export const setQuantity = [
    set(state`prestationForm.quantity`, props`value`),
]