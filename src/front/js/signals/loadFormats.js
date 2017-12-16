import { set, when, increment } from 'cerebral/operators'
import { state, props, string } from 'cerebral/tags'
import { httpGet } from '@cerebral/http/operators'

export default [
    set(state`formats.isLoading`, true),
    httpGet('/formats'), {
        success: [
            set(state`formats.isLoading`, false),
            set(state`formats.list`, props`response.result.formats`),
            set(state`formats.error`, null),
        ],
        error: [
            set(state`formats.error`, true),
            set(state`formats.isLoading`, false),
        ]
    },
]