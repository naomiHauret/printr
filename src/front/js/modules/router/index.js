//
// Cerebral router config
//

import Router from '@cerebral/router'

const router = Router({
  routes: [
    {
      path: '/',
      signal: 'homeRouted' 
    },
    {
        path: '/editor',
        signal: 'editorRouted'
    }
  ]
})

export default router