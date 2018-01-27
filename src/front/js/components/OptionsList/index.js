import Inferno from 'inferno'
import { connect } from '@cerebral/inferno'
import { state, signal } from 'cerebral/tags'

export default connect({
    options: state`prestationForm.options`,
    toggleOption: signal`toggleOption`
},
    function OptionsList({ options, toggleOption }) {
        if (options.isLoading) return <h1>Loading</h1>
        if (options.isLoading === false || (options.list !== null && options.list.length > 0)) {
            if (options.list !== null) {
                return <div>
                    {options.list.map(option => <div key={`option-${option.category}`}>
                        <b>{option.category}</b>
                        <div>
                            {option.choices.map(choice => <label name={`${option.category}`}>
                                    <input
                                        type="radio"
                                        name={`${option.category}`}
                                        onClick={e => {
                                            toggleOption({
                                                category: option.category,
                                                id: choice._id,
                                            })}
                                        }
                                        checked={choice.checked}
                                        required
                                    />
                                    {choice.option_name}
                                    {choice.option_price != 0 &&
                                    <span> (+{choice.option_price} € per copy)</span>
                                    }
                                    <br/>
                                </label>
                            )}
                        </div>
                    </div>)}
                </div>
            } else {
                return <h1>No options </h1>
            }
        }
    }
)