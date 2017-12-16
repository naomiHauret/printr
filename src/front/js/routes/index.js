//
// Inferno router
//

import Inferno from 'inferno'
import {connect} from '@cerebral/inferno'
import {state} from 'cerebral/tags'

// pages
import Home from './../pages/Home'
import Editor from './../pages/Editor'

const pages = {
  home: Home,
  editor: Editor
}

export default connect({
  currentPage: state`currentPage`
},
  function Routes ({currentPage}) {
    const Page = pages[currentPage]

    return (
    <div>
        <Page />
    </div>
    )
  }
)
