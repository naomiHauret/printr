//
// Inferno router
//

import Inferno from 'inferno'
import {connect} from '@cerebral/inferno'
import {state} from 'cerebral/tags'

// pages
import Home from './../pages/Home'
import AddPrestation from './../pages/AddPrestation'
import EditPrestation from './../pages/EditPrestation'

const pages = {
  home: Home,
  editor: AddPrestation,
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
