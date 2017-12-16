import Inferno from 'inferno'

// Components
import LoadingButton from './../LoadingButton'
import { connect } from '@cerebral/inferno'
import { state, signal } from 'cerebral/tags'

export default connect({
    user: state`user`,
    triggerLogin: signal`logUserIn`,
    triggerLogout: signal`logUserOut`,
    handleEmailChange: signal`inputEmailValue`,
    handlePasswordChange: signal`inputPasswordValue`,
    removeByeMessage: signal`removeByeMessage`,
},
    function UserGate({ user, triggerLogin, triggerLogout, handleEmailChange, handlePasswordChange, removeByeMessage}) {

        if (user.info){
            let currentUserName = user.info.first_name
            const welcomeMessage = user.visitCounter > 1
                ? `Welcome ${currentUserName} !`
                : `Nice to see you again ${currentUserName} !`
            return <div>
                <span>
                    { !user.isLoadingData && welcomeMessage}
                    <LoadingButton
                        label={user.error ? "Try again" : "Logout"}
                        isDisabled={false}
                        isLoadingData={user.isLoadingData}
                        onClick={
                            () => {
                                triggerLogout()
                                setTimeout(removeByeMessage, 4000)
                            }
                        }
                    />
                </span>
            </div>
        }
        return <div>
            {
                user.hasLoggedOut && <div>
                    See you, {user.tmpName} !
                </div>
            }
            <form
                onSubmit={
                    (e)=> {
                        e.preventDefault()
                        triggerLogin()
                    }
                }
            >
                <input
                    type="email"
                    value={user.creditentials.email}
                    onInput={(e) => handleEmailChange({ value: e.target.value })}
                    disabled={user.isLoadingData}
                />
                <input
                    type="password"
                    value={user.creditentials.password}
                    onInput={(e) => handlePasswordChange({ value: e.target.value })}
                    disabled={user.isLoadingData}
                />
                <LoadingButton
                    type="submit"
                    label={user.error ? "Try again" : "Login"}
                    isLoadingData={user.isLoadingData}
                    isDisabled={
                        user.creditentials.email.trim() === ""
                        || !(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(user.creditentials.email))
                        || user.creditentials.password.trim() === ""
                    }
                />
            </form>
            {user.error && <div>
                Oops. It seems like your e-mail and/or password is wrong.
                </div>
            }
        </div>
    }
)
