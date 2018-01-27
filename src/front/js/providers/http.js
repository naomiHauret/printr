import HttpProvider from '@cerebral/http'

const http = HttpProvider({
    baseUrl: 'http://localhost:3000/public/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
})


export default http