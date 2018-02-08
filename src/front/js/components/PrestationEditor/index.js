import Inferno from 'inferno'
import FileLoader from './../FileLoader'
import FormatsList from './../FormatsList'
import OptionsList from './../OptionsList'
import QuantityInput from './../QuantityInput'

import { connect } from '@cerebral/inferno'
import { state, signal } from 'cerebral/tags'

import styles from "./styles.css"

export default connect({
    prestationForm: state`prestationForm`,
    uploadPrestationFile: signal`uploadedPrestationFile`,
    triggerFileError: signal`errorUploadingPrestationFile`,
    setQuantity: signal`setQuantity`,
},
    function PrestationEditor({ prestationForm, uploadPrestationFile, triggerFileError, setQuantity }) {
        return <form className={styles.bgDots}>
            <div className={`flex ${styles.inEditorMaxHeight} `} >
              <FormatsList />
              <div className="flex flex-col flex-grow">
                <div className="flex flex-grow">
                  <FileLoader formatId={prestationForm.format.id} imgUrl={prestationForm.image.url} error={prestationForm.image.error} value={prestationForm.image.error != null ? prestationForm.image.url : ""} onChange={e => {
                      e.preventDefault();
                      let reader = new FileReader();
                      let file = e.target.files[0];
                      if (file.size < 2000000) {
                        reader.onloadend = () => {
                          uploadPrestationFile({
                            value: reader.result
                          });
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
                <div className="flex bg-white border-0 border-t-1 border-solid border-grey-light">
                  <OptionsList />
                  <QuantityInput onInput={e => {
                      setQuantity({ value: e.currentTarget.value });
                    }} />
                </div>
              </div>
            </div>
            <button
              className={`transition cursor-pointer transition-fast font-extrabold h-16 w-full text-xl border-0 ${prestationForm.image.url === null || prestationForm.format.id === null || prestationForm.options.filled === null || prestationForm.quantity === null ? "line-through cursor-not-allowed bg-white text-grey-light border-0 border-solid border-t-1 border-grey-light" : "text-white bg-gradient-green"}`}
              onClick={e => {
                e.preventDefault();
              }} type="submit" disabled={prestationForm.image.url === null || prestationForm.format.id === null || prestationForm.options.filled === null || prestationForm.quantity === null}>
              Add to my cart
            </button>
          </form>;
    }
)

