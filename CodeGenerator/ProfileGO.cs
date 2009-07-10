using System;
using System.Collections.Generic;
using System.Text;

namespace GURUCORE.GForm.CodeGenerator
{
    [System.Diagnostics.DebuggerStepThrough]
    public  sealed partial class ProfileGO
    {
        private string m_sName;

        private string m_sTablePrefixes;
		private string m_sDefaultNameSpace;
		private string m_sSaveFolder;
		private string m_sServerName;
		private string m_sDatabase;
		private string m_sUser;
        private string m_sPassword;

        public string Name
        {
            get { return m_sName; }
            set { m_sName = value; }
        }

        public string TablePrefixes
        {
            get { return m_sTablePrefixes; }
            set { m_sTablePrefixes = value; }
        }
        public string DefaultNameSpace
        {
            get { return m_sDefaultNameSpace; }
            set { m_sDefaultNameSpace = value; }
        }
        public string SaveFolder
        {
            get { return m_sSaveFolder; }
            set { m_sSaveFolder = value; }
        }
        public string ServerName
        {
            get { return m_sServerName; }
            set { m_sServerName = value; }
        }
        public string Database
        {
            get { return m_sDatabase; }
            set { m_sDatabase = value; }
        }
        public string User
        {
            get { return m_sUser; }
            set { m_sUser = value; }
        }
        public string Password
        {
            get { return m_sPassword; }
            set { m_sPassword = value; }
        }

    }
}
