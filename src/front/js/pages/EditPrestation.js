import Inferno from 'inferno'

import UserGate from './../components/UserGate'
import PrestationEditor from './../components/PrestationEditor'

const EditPrestation = () => (
  <div>
    <h1>Editor</h1>
    <UserGate />
    <PrestationEditor />
  </div>
);

export default EditPrestation