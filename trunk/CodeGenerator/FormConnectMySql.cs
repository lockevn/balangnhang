using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using MySql.Data.MySqlClient;

namespace GURUCORE.GForm.CodeGenerator 
{
    public partial class FormConnectMySql : FormConnect
    {
        public FormConnectMySql()
        {
            InitializeComponent();
        }

        private void btnCancel_Click(object sender, EventArgs e)
        {
            this.DialogResult = DialogResult.Cancel;
            this.Close();
        }

        private void btnConnect_Click(object sender, EventArgs e)
        {
            string sServer = txtServer.Text;
            string sDatabase = txtDatabase.Text;
            string sUser = txtUser.Text;
            string sPassword = txtPassword.Text;
            
            string sConnStr = string.Empty;
            sConnStr += "server=" + sServer + "; ";
            sConnStr += "database=" + sDatabase + "; ";
            sConnStr += "user id=" + sUser + "; ";
            sConnStr += "password=" + sPassword + "; ";

            MySqlConnection oConn = new MySqlConnection(sConnStr);
            m_oSchemaReader = new MySqlSchemaReader(oConn);

            this.DialogResult = DialogResult.OK;

            //save to registry
            GlobalOptions.GetInstance().CurrentProfile.ServerName = sServer;
            GlobalOptions.GetInstance().CurrentProfile.Database = sDatabase;
            GlobalOptions.GetInstance().CurrentProfile.User = sUser;
            GlobalOptions.GetInstance().CurrentProfile.Password = sPassword;
            // GlobalOptions.GetInstance().WriteFileRepository();
            this.Close();
        }

        private void ConnectMySql_Load(object sender, EventArgs e)
        {
            txtServer.Text = GlobalOptions.GetInstance().CurrentProfile.ServerName;
            txtDatabase.Text = GlobalOptions.GetInstance().CurrentProfile.Database;
            txtUser.Text = GlobalOptions.GetInstance().CurrentProfile.User;
            txtPassword.Text = GlobalOptions.GetInstance().CurrentProfile.Password;				
        }
    }
}
