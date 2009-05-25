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
using MySql.Data.MySqlClient;
using TalaAPI.Business;

namespace TalaAPI.Lib
{
    public class DBUtil
    {
        public static string ConnectionString = "server=quantumme.dnsalias.com;user id=root; password=quantum123!; database=tala; pooling=yes;";
        
        /// <summary>
        /// 
        /// </summary>
        /// <param name="p_sUsername"></param>
        /// <param name="p_sPassword">MD5 hashed password</param>
        public static User GetUserByUsernameAndPassword(string p_sUsername, string p_sPassword)
        {
            MySqlConnection con = new MySqlConnection(DBUtil.ConnectionString);
            con.Open();
            string strSQL = string.Format("SELECT * FROM User where u='{0}' and password='{1}';", p_sUsername, p_sPassword);
            MySqlCommand command = con.CreateCommand();
            command.CommandText = strSQL;
            MySqlDataReader reader = command.ExecuteReader();

            User ret = new User();            
            while(reader.Read())
            {
                ret.Username = reader["u"] as string;
            }
            con.Close();

            return ret;
        }
    }
}
