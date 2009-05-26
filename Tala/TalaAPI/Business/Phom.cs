using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.HtmlControls;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Xml.Linq;

namespace TalaAPI.Business
{
    public class Phom
    {
        public int Id;
        public Card[] CardArr;
        public Seat OfSeat; /*seat ma phom thuoc ve*/

        public Phom(Card[] cardArr)
        {
            this.CardArr = cardArr;
        }

    }
}
