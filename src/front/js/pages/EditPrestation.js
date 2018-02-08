import Inferno from 'inferno'

import UserGate from './../components/UserGate'
import PrestationEditor from './../components/PrestationEditor'

const EditPrestation = () => (
  <header>
    <div className="flex justify-between items-center px-2 bg-white border-b-1 border-grey-light h-24">
      <h1 data-content="Rest In Print">Rest in Print</h1>
      <UserGate />
    </div>
    <PrestationEditor />
  </header>
);

export default EditPrestation