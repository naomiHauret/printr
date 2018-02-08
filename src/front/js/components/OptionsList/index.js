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
                return <div className="flex flex-grow p-4">
                    {options.list.map(option => (
                      <div key={`option-${option.category}`} className="flex-grow">
                        <div className={`transition inline-block w-3 h-3 mr-2 rounded-full ${option.chosen !== undefined ? "bg-gradient-green shadow-green" : "bg-gradient-orange shadow-orange"}`}></div>
                        <b className="uppercase tracking-wide cursor-pointer text-grey-dark inline-block mb-2" >
                          {option.category}
                        </b>
                        <div>
                          {option.choices.map(choice => (
                            <label className="block cursor-pointer py-1" name={`${option.category}`}>
                              <input
                                type="radio"
                                name={`${option.category}`}
                                onClick={e => {
                                  toggleOption({
                                    category: option.category,
                                    id: choice._id
                                  });
                                  const uiSheet = document.querySelector('[data-flag="sheet"]')
                                  uiSheet !== null && document.querySelector('[data-flag="sheet"]').classList.toggle("changedAnimation")
                                }}
                                checked={choice.checked}
                                required
                              />
                              {choice.option_name}
                              {choice.option_price != 0 && (
                                <span>
                                  {" "}
                                  (+{choice.option_price} € per
                                  copy)
                                </span>
                              )}
                              <br />
                            </label>
                          ))}
                        </div>
                      </div>
                    ))}
                  </div>;
            } else {
                return <h1>No options </h1>
            }
        }
    }
)