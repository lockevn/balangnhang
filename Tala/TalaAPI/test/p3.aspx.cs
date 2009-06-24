using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using MySql.Data.MySqlClient;
using TalaAPI.Lib;
using TalaAPI.XMLRenderOutput;

namespace TalaAPI.test
{
    public partial class p3 : TalaPage
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            #region DEPLOY TEST: Install 100 user for Tala

            //MySqlConnection con = new MySqlConnection(DBUtil.ConnectionString);
            //con.Open();

            //MySqlCommand command = con.CreateCommand();
            //for (int i = 571; i < 1000; i++)
            //{
            //    string sUsername = i.ToString();
            //    string strSQL = string.Format("insert into `user` (u, password, balance) values('v{0}','370757d2df51ae456bf63c165fc71817',1000);", sUsername);
            //    command.CommandText = strSQL;
            //    try
            //    {
            //        object oRet = command.ExecuteNonQuery();
            //    }
            //    catch
            //    {
            //    }
            //}            

            //con.Close();

            pln("insert OK");

            #endregion
        }
    }
}
