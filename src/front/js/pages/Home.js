import Inferno from 'inferno'
import UserGate from './../components/UserGate'
import FormatsList from './../components/FormatsList'

const Home = () => <header>
    <div className="flex justify-between items-center px-2">
        <div>
            <h1 className="title" data-content="Rest In Print">Rest in Print</h1>
            <div class="title__d1"></div>
            <div class="title__d2"></div>
            <div class="title__d3"></div>
        </div>
        <UserGate />
    </div>
    <FormatsList />
</header>

export default Home