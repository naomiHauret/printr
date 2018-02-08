import Inferno from "inferno";

import UserGate from "./../components/UserGate";
import PrestationEditor from "./../components/PrestationEditor";

const AddPrestation = () => (
  <div>
    <div className="flex justify-between items-center px-2 bg-white border-0 border-b-1 border-solid border-grey-light h-24">
      <div class="relative w-1/3 h-24">
        <h1 data-content="Rest In Print" className="title text-5xl ml-2">Rest in Print</h1>
      </div>
      <UserGate />
    </div>
    <PrestationEditor />
  </div>
);

export default AddPrestation
