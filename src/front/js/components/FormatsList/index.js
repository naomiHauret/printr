import Inferno from 'inferno'
import { connect } from '@cerebral/inferno'
import { state, signal } from 'cerebral/tags'

import FormatChoice from './../FormatChoice'

export default connect({
    setChosenFormat: signal`userChoseFormat`,
    chosenFormat: state`prestationForm.format.id`,
    formats: state`formats`,
    currentPage: state`currentPage`,
},
    function FormatsList ({chosenFormat, formats, setChosenFormat, currentPage}) {
            let customListStyles = ["list-reset p-0 m-0"]
            if (currentPage !== "home") customListStyles.push("overflow-hidden overflow-y-scroll bg-grey-lightest text-grey w-64 shadow-inner")
            else customListStyles.push("flex")
            customListStyles= customListStyles.join(" ")

            if (formats.isLoading) return <h1>Loading</h1>
            if (formats.isLoading === false || (formats.list  !== null && formats.list.length > 0)) {
                if(formats.list !== null) {
                    return <ul className={customListStyles}>
                        {formats.list.map(format => (
                            <li
                                key={`format-${format._id}`}
                                className={
                                `transition-fast ${format._id === chosenFormat ? "shadow-lg text-white bg-gradient-purple" : "md:hover:text-purple" }`
                              }
                            >
                            <FormatChoice
                              value={format._id}
                              name={format.format_name}
                              dimensions={
                                format.format_dimensions
                              }
                              price={format.format_price}
                              handleClick={e => {
                                    setChosenFormat({
                                        value: e.target.value
                                    })
                                    const uiSheet = document.querySelector('[data-flag="sheet"]')
                                    uiSheet !== null && document.querySelector('[data-flag="sheet"]').classList.toggle("changedAnimation")
                                }
                              }
                              checked={
                                format._id === chosenFormat
                              }
                            />
                          </li>
                        ))}
                      </ul>;
                } else {
                    return <h1>No formats </h1>
                }
            }             
    }
)