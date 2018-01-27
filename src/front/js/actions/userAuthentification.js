import { httpGet, httpPost } from '@cerebral/http/operators'
import { set, increment, wait, when } from 'cerebral/operators'
import { state, props, string } from 'cerebral/tags'
import { resolveObject } from 'function-tree'

export const logUserIn = [
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
            set(state`user.displayWelcomeMessage`, true),
            wait(5000),
            set(state`user.displayWelcomeMessage`, true),
        ],
        error: [
            set(state`user.error`, true),
            set(state`user.creditentials.password`, ''),
            set(state`user.isLoading`, false),
        ]
    },
]

export const logUserOut = [
    set(state`user.isLoading`, true),
    httpGet('/disconnect'), {
        success: [
            set(state`user.isLoading`, false),
            set(state`user.error`, null),
            set(state`user.displayByeMessage`, true),
            set(state`user.tmpName`, state`user.info.first_name`),
            set(state`user.info`, null),
            wait(5000),
            set(state`user.displayByeMessage`, null),
            set(state`user.tmpName`, null)
        ],
        error: [
            set(state`user.error`, true),
            set(state`user.isLoading`, false),
        ]
    },
]

export const inputEmailValue = [
    when(props`value`), {
        true: set(state`user.creditentials.email`, string`${props`value`}`),
        false: set(state`user.creditentials.email`, '')
    }
]

export const inputPasswordValue = [
    when(props`value`), {
        true: set(state`user.creditentials.password`, string`${props`value`}`),
        false: set(state`user.creditentials.password`, '')
    }
]