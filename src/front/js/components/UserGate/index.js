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
},
    function UserGate({ user, triggerLogin, triggerLogout, handleEmailChange, handlePasswordChange}) {

        if (user.info){
            let currentUserName = user.info.first_name
            const welcomeMessage = user.visitCounter > 1
                ? `Welcome ${currentUserName} !`
                : `Nice to see you again ${currentUserName} !`
            return <div>
                <div className="flex">
                    <span className="p-3 block h-100 rounded-l-full border-solid border-1 border-r-0 border-grey text-base text-grey-dark">
                        {!user.isLoadingData && user.displayWelcomeMessage && welcomeMessage}
                    </span>
                    <LoadingButton
                        label={user.error ? "Try again" : "Logout"}
                        isDisabled={false}
                        isLoadingData={user.isLoadingData}
                        onClick={triggerLogout}
                        classes="rounded-r-full border-0 p-3 pr-6 bg-white border-grey border-1 border-solid text-base text-grey-darkest"
                    />
                </div>
            </div>
        }
        return <div>
            {
                user.displayByeMessage && <div></div>
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
                    className="w-64 rounded-l-full border-solid border-r-0 border-1 border-grey p-3 text-grey-dark text-base"
                    value={user.creditentials.email}
                    onInput={(e) => handleEmailChange({ value: e.target.value })}
                    disabled={user.isLoadingData}
                />
                <input
                    type="password"
                    className="border-solid border-1 border-grey p-3 text-grey-dark text-base"
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
                    classes="rounded-r-full border-solid bg-white border-grey border-l-0 p-3 pr-6 border-1 text-base text-grey-dark"
                />
            </form>
            {user.error && <div>
                Oops. It seems like your e-mail and/or password is wrong.
                </div>
            }
        </div>
    }
)
