using System;
using System.Collections;

namespace GURUCORE.GForm.CodeGenerator
{
	public class SchemaObject
	{
		private ArrayList m_arrProperties;
		private string m_sObjectType;
		private string m_sPrimaryKey;
		private string m_sName;
        private TypeMapper m_oTypeMapper;

        public string ObjectType
        {
            get
            {
                return m_sObjectType;
            }
        }
        public string PrimaryKey
        {
            get
            {
                return m_sPrimaryKey;
            }
        }
        public string Name
        {
            get
            {
                return m_sName;
            }
        }
        public ArrayList Properties
        {
            get
            {
                return m_arrProperties;
            }
        }
        public TypeMapper TypeMapper
        {
            get
            {
                return m_oTypeMapper;
            }
        }

		public SchemaObject(string p_sObjectType, string p_sName, string p_sPrimaryKey, TypeMapper p_oTypeMapper)
		{
			m_arrProperties = new ArrayList();
			m_sObjectType = p_sObjectType;
			m_sName = p_sName;
			m_sPrimaryKey = p_sPrimaryKey;

			m_oTypeMapper = p_oTypeMapper;
		}
	}
}
