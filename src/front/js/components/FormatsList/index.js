import Inferno from 'inferno'
import { connect } from '@cerebral/inferno'
import { state, signal } from 'cerebral/tags'

import FormatChoice from './../FormatChoice'

export default connect({
    setChosenFormat: signal`userChoseFormat`,
    chosenFormat: state`prestationForm.format.id`,
    formats: state`formats`,
},
    function FormatsList ({chosenFormat, formats, setChosenFormat}) {
            if (formats.isLoading) return <h1>Loading</h1>
            if (formats.isLoading === false || (formats.list  !== null && formats.list.length > 0)) {
                if(formats.list !== null) {
                    return <ul>
                        {formats.list.map(format => <li key={`format-${format._id}`}>
                            <FormatChoice
                                value={format._id}
                                name={format.format_name}
                                dimensions={format.format_dimensions}
                                price={format.format_price}
                                handleClick={e => setChosenFormat({ value: e.target.value })}
                                checked={format._id === chosenFormat}
                            />
                        </li>)}
                    </ul>
                } else {
                    return <h1>No formats </h1>
                }
            }             
    }
)