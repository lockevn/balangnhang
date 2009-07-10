using System;
using System.Drawing;
using System.Collections;
using System.ComponentModel;
using System.Windows.Forms;

namespace GURUCORE.GForm.CodeGenerator
{	
	public class Option : System.Windows.Forms.Form
	{
		private System.Windows.Forms.Label label1;
		private System.Windows.Forms.Label label2;
		private System.Windows.Forms.Label label3;
		private System.Windows.Forms.TextBox txtTablePrefixes;
		private System.Windows.Forms.TextBox txtDefaultNameSpace;
		private System.Windows.Forms.TextBox txtSaveFolder;
		private System.Windows.Forms.Button btnCancel;
		private System.Windows.Forms.Button btnApply;
		private System.Windows.Forms.Button btnBrowse;
		private System.Windows.Forms.FolderBrowserDialog dlgBrowseFolder;
        private ComboBox cboProfile;
        private Label label4;
        private TextBox txtServerName;
        private TextBox txtDatabase;
        private TextBox txtUser;
        private TextBox txtPassword;
        private Label label5;
        private Label label6;
        private Label label7;
        private Label label8;
		/// <summary>
		/// Required designer variable.
		/// </summary>
		private System.ComponentModel.Container components = null;

		public Option()
		{
			//
			// Required for Windows Form Designer support
			//
			InitializeComponent();			
		}

		/// <summary>
		/// Clean up any resources being used.
		/// </summary>
		protected override void Dispose( bool disposing )
		{
			if( disposing )
			{
				if(components != null)
				{
					components.Dispose();
				}
			}
			base.Dispose( disposing );
		}

		#region Windows Form Designer generated code
		/// <summary>
		/// Required method for Designer support - do not modify
		/// the contents of this method with the code editor.
		/// </summary>
		private void InitializeComponent()
		{
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(Option));
            this.label1 = new System.Windows.Forms.Label();
            this.txtTablePrefixes = new System.Windows.Forms.TextBox();
            this.txtDefaultNameSpace = new System.Windows.Forms.TextBox();
            this.label2 = new System.Windows.Forms.Label();
            this.txtSaveFolder = new System.Windows.Forms.TextBox();
            this.label3 = new System.Windows.Forms.Label();
            this.btnCancel = new System.Windows.Forms.Button();
            this.btnApply = new System.Windows.Forms.Button();
            this.btnBrowse = new System.Windows.Forms.Button();
            this.dlgBrowseFolder = new System.Windows.Forms.FolderBrowserDialog();
            this.cboProfile = new System.Windows.Forms.ComboBox();
            this.label4 = new System.Windows.Forms.Label();
            this.txtServerName = new System.Windows.Forms.TextBox();
            this.txtDatabase = new System.Windows.Forms.TextBox();
            this.txtUser = new System.Windows.Forms.TextBox();
            this.txtPassword = new System.Windows.Forms.TextBox();
            this.label5 = new System.Windows.Forms.Label();
            this.label6 = new System.Windows.Forms.Label();
            this.label7 = new System.Windows.Forms.Label();
            this.label8 = new System.Windows.Forms.Label();
            this.SuspendLayout();
            // 
            // label1
            // 
            this.label1.Font = new System.Drawing.Font("Arial", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(16, 56);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(107, 20);
            this.label1.TabIndex = 0;
            this.label1.Text = "Table Prefixes";
            this.label1.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // txtTablePrefixes
            // 
            this.txtTablePrefixes.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.txtTablePrefixes.Font = new System.Drawing.Font("Courier New", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtTablePrefixes.Location = new System.Drawing.Point(127, 56);
            this.txtTablePrefixes.Name = "txtTablePrefixes";
            this.txtTablePrefixes.Size = new System.Drawing.Size(434, 20);
            this.txtTablePrefixes.TabIndex = 0;
            // 
            // txtDefaultNameSpace
            // 
            this.txtDefaultNameSpace.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.txtDefaultNameSpace.Font = new System.Drawing.Font("Courier New", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtDefaultNameSpace.Location = new System.Drawing.Point(127, 80);
            this.txtDefaultNameSpace.Name = "txtDefaultNameSpace";
            this.txtDefaultNameSpace.Size = new System.Drawing.Size(434, 20);
            this.txtDefaultNameSpace.TabIndex = 1;
            // 
            // label2
            // 
            this.label2.Font = new System.Drawing.Font("Arial", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(16, 80);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(107, 20);
            this.label2.TabIndex = 2;
            this.label2.Text = "Default Namespace";
            this.label2.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // txtSaveFolder
            // 
            this.txtSaveFolder.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.txtSaveFolder.Font = new System.Drawing.Font("Courier New", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtSaveFolder.Location = new System.Drawing.Point(127, 104);
            this.txtSaveFolder.Name = "txtSaveFolder";
            this.txtSaveFolder.ReadOnly = true;
            this.txtSaveFolder.Size = new System.Drawing.Size(402, 20);
            this.txtSaveFolder.TabIndex = 11;
            this.txtSaveFolder.TabStop = false;
            // 
            // label3
            // 
            this.label3.Font = new System.Drawing.Font("Arial", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label3.Location = new System.Drawing.Point(16, 104);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(107, 20);
            this.label3.TabIndex = 4;
            this.label3.Text = "Save Folder";
            this.label3.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // btnCancel
            // 
            this.btnCancel.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.btnCancel.DialogResult = System.Windows.Forms.DialogResult.Cancel;
            this.btnCancel.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnCancel.Location = new System.Drawing.Point(484, 233);
            this.btnCancel.Name = "btnCancel";
            this.btnCancel.Size = new System.Drawing.Size(75, 26);
            this.btnCancel.TabIndex = 8;
            this.btnCancel.Text = "Close";
            this.btnCancel.Click += new System.EventHandler(this.btnCancel_Click);
            // 
            // btnApply
            // 
            this.btnApply.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.btnApply.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnApply.Location = new System.Drawing.Point(404, 233);
            this.btnApply.Name = "btnApply";
            this.btnApply.Size = new System.Drawing.Size(75, 26);
            this.btnApply.TabIndex = 7;
            this.btnApply.Text = "Save";
            this.btnApply.Click += new System.EventHandler(this.btnApply_Click);
            // 
            // btnBrowse
            // 
            this.btnBrowse.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnBrowse.Location = new System.Drawing.Point(537, 104);
            this.btnBrowse.Name = "btnBrowse";
            this.btnBrowse.Size = new System.Drawing.Size(24, 20);
            this.btnBrowse.TabIndex = 2;
            this.btnBrowse.Text = "...";
            this.btnBrowse.Click += new System.EventHandler(this.btnBrowse_Click);
            // 
            // cboProfile
            // 
            this.cboProfile.BackColor = System.Drawing.Color.PaleTurquoise;
            this.cboProfile.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.cboProfile.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.cboProfile.Font = new System.Drawing.Font("Courier New", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.cboProfile.FormattingEnabled = true;
            this.cboProfile.Location = new System.Drawing.Point(128, 12);
            this.cboProfile.Name = "cboProfile";
            this.cboProfile.Size = new System.Drawing.Size(233, 26);
            this.cboProfile.TabIndex = 9;
            this.cboProfile.SelectedIndexChanged += new System.EventHandler(this.cboProfile_SelectedIndexChanged);
            // 
            // label4
            // 
            this.label4.BackColor = System.Drawing.Color.PaleTurquoise;
            this.label4.Font = new System.Drawing.Font("Arial", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.Location = new System.Drawing.Point(19, 12);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(111, 23);
            this.label4.TabIndex = 10;
            this.label4.Text = "Select Profile";
            this.label4.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // txtServerName
            // 
            this.txtServerName.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.txtServerName.Font = new System.Drawing.Font("Courier New", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtServerName.Location = new System.Drawing.Point(127, 128);
            this.txtServerName.Name = "txtServerName";
            this.txtServerName.Size = new System.Drawing.Size(434, 20);
            this.txtServerName.TabIndex = 3;
            // 
            // txtDatabase
            // 
            this.txtDatabase.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.txtDatabase.Font = new System.Drawing.Font("Courier New", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtDatabase.Location = new System.Drawing.Point(127, 152);
            this.txtDatabase.Name = "txtDatabase";
            this.txtDatabase.Size = new System.Drawing.Size(434, 20);
            this.txtDatabase.TabIndex = 4;
            // 
            // txtUser
            // 
            this.txtUser.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.txtUser.Font = new System.Drawing.Font("Courier New", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtUser.Location = new System.Drawing.Point(127, 176);
            this.txtUser.Name = "txtUser";
            this.txtUser.Size = new System.Drawing.Size(434, 20);
            this.txtUser.TabIndex = 5;
            // 
            // txtPassword
            // 
            this.txtPassword.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.txtPassword.Font = new System.Drawing.Font("Courier New", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtPassword.Location = new System.Drawing.Point(127, 200);
            this.txtPassword.Name = "txtPassword";
            this.txtPassword.Size = new System.Drawing.Size(434, 20);
            this.txtPassword.TabIndex = 6;
            this.txtPassword.UseSystemPasswordChar = true;
            // 
            // label5
            // 
            this.label5.Font = new System.Drawing.Font("Arial", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(16, 176);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(107, 20);
            this.label5.TabIndex = 17;
            this.label5.Text = "User";
            this.label5.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label6
            // 
            this.label6.Font = new System.Drawing.Font("Arial", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label6.Location = new System.Drawing.Point(16, 152);
            this.label6.Name = "label6";
            this.label6.Size = new System.Drawing.Size(107, 20);
            this.label6.TabIndex = 16;
            this.label6.Text = "Database";
            this.label6.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label7
            // 
            this.label7.Font = new System.Drawing.Font("Arial", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label7.Location = new System.Drawing.Point(16, 128);
            this.label7.Name = "label7";
            this.label7.Size = new System.Drawing.Size(107, 20);
            this.label7.TabIndex = 15;
            this.label7.Text = "ServerName";
            this.label7.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label8
            // 
            this.label8.Font = new System.Drawing.Font("Arial", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label8.Location = new System.Drawing.Point(16, 200);
            this.label8.Name = "label8";
            this.label8.Size = new System.Drawing.Size(107, 20);
            this.label8.TabIndex = 18;
            this.label8.Text = "Password";
            this.label8.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // Option
            // 
            this.AcceptButton = this.btnApply;
            this.AutoScaleBaseSize = new System.Drawing.Size(5, 13);
            this.BackColor = System.Drawing.Color.Gainsboro;
            this.CancelButton = this.btnCancel;
            this.ClientSize = new System.Drawing.Size(582, 276);
            this.Controls.Add(this.cboProfile);
            this.Controls.Add(this.label8);
            this.Controls.Add(this.label5);
            this.Controls.Add(this.label6);
            this.Controls.Add(this.label7);
            this.Controls.Add(this.txtPassword);
            this.Controls.Add(this.txtUser);
            this.Controls.Add(this.txtDatabase);
            this.Controls.Add(this.txtServerName);
            this.Controls.Add(this.label4);
            this.Controls.Add(this.btnBrowse);
            this.Controls.Add(this.btnApply);
            this.Controls.Add(this.btnCancel);
            this.Controls.Add(this.txtSaveFolder);
            this.Controls.Add(this.label3);
            this.Controls.Add(this.txtDefaultNameSpace);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.txtTablePrefixes);
            this.Controls.Add(this.label1);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.KeyPreview = true;
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.Name = "Option";
            this.Opacity = 0.9;
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Option";
            this.TopMost = true;
            this.KeyUp += new System.Windows.Forms.KeyEventHandler(this.Option_KeyUp);
            this.Load += new System.EventHandler(this.Option_Load);
            this.ResumeLayout(false);
            this.PerformLayout();

		}
		#endregion


        private string m_sCurrentProfile = string.Empty;
        public string CurrentProfile
        {
            get { return m_sCurrentProfile; }
            set { m_sCurrentProfile = value; }
        }


		private void btnBrowse_Click(object sender, System.EventArgs e)
		{
			dlgBrowseFolder.SelectedPath = txtSaveFolder.Text;
			if (dlgBrowseFolder.ShowDialog() == DialogResult.OK)
			{
				txtSaveFolder.Text = dlgBrowseFolder.SelectedPath;
			}
		}

		private void btnCancel_Click(object sender, System.EventArgs e)
		{
            GlobalOptions oGlobalOption = GlobalOptions.GetInstance();
            oGlobalOption.CurrentProfileName = cboProfile.SelectedValue.ToString();
			this.Close();
		}

		private void btnApply_Click(object sender, System.EventArgs e)
		{
			GlobalOptions oGlobalOption = GlobalOptions.GetInstance();
            ApplyFormToProfile(cboProfile.SelectedValue.ToString());
		}

		private void Option_Load(object sender, System.EventArgs e)
		{		
			GlobalOptions oGlobalOption = GlobalOptions.GetInstance();
            cboProfile.DataSource = oGlobalOption.GetProfileList();
            cboProfile.SelectedItem = GlobalOptions.GetInstance().CurrentProfileName;
		}

        private void cboProfile_SelectedIndexChanged(object sender, EventArgs e)
        {            
            LoadProfileToForm(cboProfile.SelectedValue.ToString());
        }

        private void LoadProfileToForm(string p_sProfileName)
        {
            GlobalOptions oGlobal = GlobalOptions.GetInstance();
            txtTablePrefixes.Text = oGlobal.GetProfile(p_sProfileName).TablePrefixes;
            txtDefaultNameSpace.Text = oGlobal.GetProfile(p_sProfileName).DefaultNameSpace;
            txtSaveFolder.Text = oGlobal.GetProfile(p_sProfileName).SaveFolder;
            txtServerName.Text = oGlobal.GetProfile(p_sProfileName).ServerName;
            txtDatabase.Text = oGlobal.GetProfile(p_sProfileName).Database;
            txtUser.Text = oGlobal.GetProfile(p_sProfileName).User;
            txtPassword.Text = oGlobal.GetProfile(p_sProfileName).Password;
        }

        private void ApplyFormToProfile(string p_sProfileName)
        {
            GlobalOptions oGlobal = GlobalOptions.GetInstance();
            oGlobal.GetProfile(p_sProfileName).TablePrefixes = txtTablePrefixes.Text;
            oGlobal.GetProfile(p_sProfileName).DefaultNameSpace = txtDefaultNameSpace.Text;
            oGlobal.GetProfile(p_sProfileName).SaveFolder = txtSaveFolder.Text;
            oGlobal.GetProfile(p_sProfileName).ServerName = txtServerName.Text;
            oGlobal.GetProfile(p_sProfileName).Database = txtDatabase.Text;
            oGlobal.GetProfile(p_sProfileName).User = txtUser.Text;
            oGlobal.GetProfile(p_sProfileName).Password = txtPassword.Text;
        }

        private void Option_KeyUp(object sender, KeyEventArgs e)
        {
            if (e.KeyCode == Keys.F4)
            {
                cboProfile.Focus();
            }
        }

	}
}
