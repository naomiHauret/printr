import Inferno from 'inferno'
import FileLoader from './../FileLoader'
import FormatsList from './../FormatsList'
import OptionsList from './../OptionsList'
import QuantityInput from './../QuantityInput'

import { connect } from '@cerebral/inferno'
import { state, signal } from 'cerebral/tags'

export default connect({
    prestationForm: state`prestationForm`,
    uploadPrestationFile: signal`uploadedPrestationFile`,
    triggerFileError: signal`errorUploadingPrestationFile`,
    setQuantity: signal`setQuantity`,
},
    function PrestationEditor({ prestationForm, uploadPrestationFile, triggerFileError, setQuantity }) {
        return <form>
            <div style="display: flex;">
              <FormatsList />
              <FileLoader imgUrl={prestationForm.image.url} error={prestationForm.image.error} value={prestationForm.image.error != null ? prestationForm.image.url : ""} onChange={e => {
                  e.preventDefault();
                  let reader = new FileReader();
                  let file = e.target.files[0];
                  if (file.size < 2000000) {
                    reader.onloadend = () => {
                      uploadPrestationFile({ value: reader.result });
                    };

                    reader.readAsDataURL(file);
                  } else {
                    triggerFileError({
                      value:
                        "Oops, seems like your file is more than 2MB. Try again with a version lighter than 2MB."
                    });
                  }
                }} />
            </div>
            <OptionsList />
            <QuantityInput onInput={e => {
                setQuantity({ value: e.currentTarget.value });
              }}
            />
            <br/>
            <button onClick={e => {
                e.preventDefault();
              }} type="submit" disabled={prestationForm.image.url === null || prestationForm.format.id === null || prestationForm.options.filled === null || prestationForm.quantity === null}>
              Submit
            </button>
          </form>;
    }
)

