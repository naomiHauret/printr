//
// Cerebral router config
//

import Router from '@cerebral/router'

const router = Router({
  routes: [
    {
      path: "/",
      signal: "homeRouted"
    },
    {
      path: "/editor",
      signal: "editorAddPrestationRouted"
    },
    {
      path: "/editor/:id",
      signal: "editorEditPrestationRouted"
    }
  ]
});

export default router