using System;
using System.Collections;
using System.ComponentModel;
using System.Drawing;
using System.Windows.Forms;
using System.Data.SqlClient;

namespace GURUCORE.GForm.CodeGenerator
{
	public class ConnectSqlServer : CodeGenerator.Connect
	{
		private System.Windows.Forms.Label label1;
		private System.Windows.Forms.TextBox txtServer;
		private System.Windows.Forms.TextBox txtDatabase;
		private System.Windows.Forms.TextBox txtUser;
		private System.Windows.Forms.TextBox txtPassword;
		private System.Windows.Forms.Button btnConnect;
		private System.Windows.Forms.Button btnCancel;
		private System.Windows.Forms.Label label2;
		private System.Windows.Forms.Label label3;
		private System.Windows.Forms.Label label4;
		private System.ComponentModel.IContainer components = null;

		public ConnectSqlServer()
		{
			// This call is required by the Windows Form Designer.
			InitializeComponent();		
		}

		/// <summary>
		/// Clean up any resources being used.
		/// </summary>
		protected override void Dispose( bool disposing )
		{
			if( disposing )
			{
				if (components != null) 
				{
					components.Dispose();
				}
			}
			base.Dispose( disposing );
		}

		#region Designer generated code
		/// <summary>
		/// Required method for Designer support - do not modify
		/// the contents of this method with the code editor.
		/// </summary>
		private void InitializeComponent()
		{
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(ConnectSqlServer));
            this.txtServer = new System.Windows.Forms.TextBox();
            this.txtDatabase = new System.Windows.Forms.TextBox();
            this.txtUser = new System.Windows.Forms.TextBox();
            this.txtPassword = new System.Windows.Forms.TextBox();
            this.btnConnect = new System.Windows.Forms.Button();
            this.btnCancel = new System.Windows.Forms.Button();
            this.label1 = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            this.label3 = new System.Windows.Forms.Label();
            this.label4 = new System.Windows.Forms.Label();
            this.SuspendLayout();
            // 
            // txtServer
            // 
            this.txtServer.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.txtServer.Location = new System.Drawing.Point(76, 8);
            this.txtServer.Name = "txtServer";
            this.txtServer.Size = new System.Drawing.Size(313, 20);
            this.txtServer.TabIndex = 0;
            // 
            // txtDatabase
            // 
            this.txtDatabase.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.txtDatabase.Location = new System.Drawing.Point(76, 32);
            this.txtDatabase.Name = "txtDatabase";
            this.txtDatabase.Size = new System.Drawing.Size(313, 20);
            this.txtDatabase.TabIndex = 1;
            // 
            // txtUser
            // 
            this.txtUser.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.txtUser.Location = new System.Drawing.Point(76, 56);
            this.txtUser.Name = "txtUser";
            this.txtUser.Size = new System.Drawing.Size(313, 20);
            this.txtUser.TabIndex = 2;
            // 
            // txtPassword
            // 
            this.txtPassword.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.txtPassword.Location = new System.Drawing.Point(76, 80);
            this.txtPassword.Name = "txtPassword";
            this.txtPassword.PasswordChar = '*';
            this.txtPassword.Size = new System.Drawing.Size(313, 20);
            this.txtPassword.TabIndex = 3;
            // 
            // btnConnect
            // 
            this.btnConnect.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnConnect.Location = new System.Drawing.Point(207, 113);
            this.btnConnect.Name = "btnConnect";
            this.btnConnect.Size = new System.Drawing.Size(88, 24);
            this.btnConnect.TabIndex = 4;
            this.btnConnect.Text = "Connect";
            this.btnConnect.Click += new System.EventHandler(this.btnConnect_Click);
            // 
            // btnCancel
            // 
            this.btnCancel.DialogResult = System.Windows.Forms.DialogResult.Cancel;
            this.btnCancel.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnCancel.Location = new System.Drawing.Point(303, 113);
            this.btnCancel.Name = "btnCancel";
            this.btnCancel.Size = new System.Drawing.Size(88, 24);
            this.btnCancel.TabIndex = 5;
            this.btnCancel.Text = "Cancel";
            this.btnCancel.Click += new System.EventHandler(this.btnCancel_Click);
            // 
            // label1
            // 
            this.label1.Location = new System.Drawing.Point(8, 8);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(62, 18);
            this.label1.TabIndex = 6;
            this.label1.Text = "Server";
            this.label1.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label2
            // 
            this.label2.Location = new System.Drawing.Point(8, 32);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(62, 18);
            this.label2.TabIndex = 7;
            this.label2.Text = "Database";
            this.label2.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label3
            // 
            this.label3.Location = new System.Drawing.Point(8, 56);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(62, 18);
            this.label3.TabIndex = 8;
            this.label3.Text = "User";
            this.label3.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label4
            // 
            this.label4.Location = new System.Drawing.Point(8, 82);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(62, 18);
            this.label4.TabIndex = 0;
            this.label4.Text = "Password";
            // 
            // ConnectSqlServer
            // 
            this.AcceptButton = this.btnConnect;
            this.AutoScaleBaseSize = new System.Drawing.Size(5, 13);
            this.BackColor = System.Drawing.Color.Gainsboro;
            this.CancelButton = this.btnCancel;
            this.ClientSize = new System.Drawing.Size(398, 143);
            this.Controls.Add(this.txtPassword);
            this.Controls.Add(this.txtUser);
            this.Controls.Add(this.txtDatabase);
            this.Controls.Add(this.txtServer);
            this.Controls.Add(this.label4);
            this.Controls.Add(this.label3);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.btnCancel);
            this.Controls.Add(this.btnConnect);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Name = "ConnectSqlServer";
            this.Opacity = 0.9;
            this.Text = "Connect to SQL Server";
            this.Load += new System.EventHandler(this.ConnectSqlServer_Load);
            this.ResumeLayout(false);
            this.PerformLayout();

		}
		#endregion

		private void btnConnect_Click(object sender, System.EventArgs e)
		{
			string sServer = txtServer.Text;
			string sDatabase = txtDatabase.Text;
			string sUser = txtUser.Text;
			string sPassword = txtPassword.Text;

			string sConnStr = string.Empty;
			sConnStr += "server = " + sServer + "; ";
			sConnStr += "initial catalog = " + sDatabase + "; ";
			sConnStr += "user id = " + sUser + "; ";
			sConnStr += "pwd = " + sPassword + "; ";

			SqlConnection oConn = new SqlConnection(sConnStr);
			m_oSchemaReader = new SqlServerSchemaReader(oConn);

			this.DialogResult = DialogResult.OK;
			
			//save to registry
            GlobalOptions.GetInstance().CurrentProfile.ServerName = sServer;
            GlobalOptions.GetInstance().CurrentProfile.Database = sDatabase;
            GlobalOptions.GetInstance().CurrentProfile.User = sUser;
            GlobalOptions.GetInstance().CurrentProfile.Password = sPassword;
			// GlobalOptions.GetInstance().WriteFileRepository();
			this.Close();
		}

		private void btnCancel_Click(object sender, System.EventArgs e)
		{
			this.DialogResult = DialogResult.Cancel;
			this.Close();
		}

		private void ConnectSqlServer_Load(object sender, System.EventArgs e)
		{			
            txtServer.Text = GlobalOptions.GetInstance().CurrentProfile.ServerName;
            txtDatabase.Text = GlobalOptions.GetInstance().CurrentProfile.Database;
            txtUser.Text = GlobalOptions.GetInstance().CurrentProfile.User;
            txtPassword.Text = GlobalOptions.GetInstance().CurrentProfile.Password;				
		}
	}
}

