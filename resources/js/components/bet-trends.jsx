// import React from 'react'
import '../../css/bet-arnie.css'

function BetTrends() {
  return (
    //  /* className="trends limit-on-tab" */
    <div>
      <div>
        <div className="bet-yellow h6 m-0 rounded-top flex flex-row justify-around items-center py-3">
          <span className="text-black text-xl">Game Fight Results</span>
          <div className="uppercase bg-black py-2 px-4 text-red-500">LAST CALL</div>
        </div>
        {' '}
        <div className="trend-legend gp-panel-bg d-flex justify-content-between align-items-center py-3 px-2">
          <div className="bad-cont d-inline-flex align-items-center mr-1">
            <span className="count rounded-circle trend-meron">65</span>
            {' '}
            <p className="mb-0 count-label">Meron</p>
          </div>
          {' '}
          <div className="bad-cont d-inline-flex align-items-center mr-1">
            <span className="count rounded-circle trend-wala">55</span>
            {' '}
            <p className="mb-0 count-label">Wala</p>
          </div>
          {' '}
          <div className="bad-cont d-inline-flex align-items-center mr-1">
            <span className="count rounded-circle trend-draw">1</span>
            {' '}
            <p className="mb-0 count-label">Draw</p>
          </div>
          {' '}
          <div className="bad-cont d-inline-flex align-items-center">
            <span className="count rounded-circle bg-secondary">4</span>
            {' '}
            <p className="mb-0 count-label">Cancelled</p>
          </div>
        </div>
        {' '}
        <div className="px-3 pb-3 gp-panel-bg rounded-bottom landscape-trend" style={{ position: 'relative' }}>
          <div className="table-responsive pb-3 gp-scroll">
            <table className="trend-table">
              <tbody>
                <tr>
                  <td><div className="trend-item trend-meron">46</div></td>
                  <td><div className="trend-item trend-meron">50</div></td>
                  <td><div className="trend-item trend-wala">53</div></td>
                  <td><div className="trend-item trend-wala">55</div></td>
                  <td><div className="trend-item trend-meron">56</div></td>
                  <td><div className="trend-item trend-wala">59</div></td>
                  <td><div className="trend-item trend-meron">60</div></td>
                  <td><div className="trend-item trend-wala">61</div></td>
                  <td><div className="trend-item trend-meron">62</div></td>
                  <td><div className="trend-item trend-wala">65</div></td>
                  <td><div className="trend-item trend-meron">66</div></td>
                  <td><div className="trend-item trend-wala">67</div></td>
                  <td><div className="trend-item trend-meron">68</div></td>
                  <td><div className="trend-item trend-wala">69</div></td>
                  <td><div className="trend-item trend-meron">71</div></td>
                  <td><div className="trend-item trend-meron">75</div></td>
                  <td><div className="trend-item trend-wala">78</div></td>
                  <td><div className="trend-item trend-meron">80</div></td>
                  <td><div className="trend-item trend-meron">84</div></td>
                  <td><div className="trend-item trend-meron">88</div></td>
                  <td><div className="trend-item trend-wala">89</div></td>
                  <td><div className="trend-item trend-meron">90</div></td>
                  <td><div className="trend-item trend-wala">91</div></td>
                  <td><div className="trend-item trend-meron">93</div></td>
                  <td><div className="trend-item trend-cancel">×</div></td>
                  <td><div className="trend-item trend-wala">96</div></td>
                  <td><div className="trend-item trend-meron">97</div></td>
                  <td><div className="trend-item trend-wala">98</div></td>
                  <td><div className="trend-item trend-cancel">×</div></td>
                  <td><div className="trend-item trend-wala">100</div></td>
                  <td><div className="trend-item trend-cancel">×</div></td>
                  <td><div className="trend-item trend-wala">104</div></td>
                  <td><div className="trend-item trend-meron">106</div></td>
                  <td><div className="trend-item trend-wala">109</div></td>
                  <td><div className="trend-item trend-meron">110</div></td>
                  <td><div className="trend-item trend-wala">114</div></td>
                  <td><div className="trend-item trend-meron">115</div></td>
                  <td><div className="trend-item trend-wala">116</div></td>
                  <td><div className="trend-item trend-meron">120</div></td>
                  <td><div className="trend-item trend-wala">121</div></td>
                  <td><div className="trend-item trend-meron">122</div></td>
                  <td><div className="trend-item trend-wala">124</div></td>
                  <td><div className="trend-item trend-meron">125</div></td>
                </tr>
                <tr>
                  <td><div className="trend-item trend-meron">47</div></td>
                  <td><div className="trend-item trend-meron">51</div></td>
                  <td><div className="trend-item trend-draw">54</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">57</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">63</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">70</div></td>
                  <td><div className="trend-item trend-meron">72</div></td>
                  <td><div className="trend-item trend-meron">76</div></td>
                  <td><div className="trend-item trend-wala">79</div></td>
                  <td><div className="trend-item trend-meron">81</div></td>
                  <td><div className="trend-item trend-meron">85</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">92</div></td>
                  <td><div className="trend-item trend-meron">94</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">101</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">105</div></td>
                  <td><div className="trend-item trend-meron">107</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">111</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">117</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">123</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                </tr>
                <tr>
                  <td><div className="trend-item trend-meron">48</div></td>
                  <td><div className="trend-item trend-meron">52</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">58</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">64</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">73</div></td>
                  <td><div className="trend-item trend-meron">77</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">82</div></td>
                  <td><div className="trend-item trend-meron">86</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">102</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">108</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">112</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">118</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                </tr>
                <tr>
                  <td><div className="trend-item trend-meron">49</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">74</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">83</div></td>
                  <td><div className="trend-item trend-meron">87</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">113</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">119</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  )
}

export default BetTrends
