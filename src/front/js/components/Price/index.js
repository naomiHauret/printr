import Inferno from "inferno"

import { connect } from "@cerebral/inferno";
import { state, signal } from "cerebral/tags";

export default connect(
  {
    prestationForm: state`prestationForm`,
    formats: state`formats.list`
  },
  function PrestationEditor({
    prestationForm,
    formats,
  }) {
    let formatPrice = 0
    return (
        <div>

            <span>Total (no taxes): {
                {formats.map((format) => {
                  if(format._id === prestationForm.format.id) {
                    formatPrice = parseInt(format.format_price)
                  }
                })}
                { formatPrice }
            } </span>
        </div>
    );
  }
);
